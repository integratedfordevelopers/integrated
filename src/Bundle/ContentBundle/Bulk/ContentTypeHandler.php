<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Services\SearchContentReferenced;
use Integrated\Common\Bulk\Action\HandlerInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Queue\Queue;
use Integrated\Common\Solr\Indexer\Job;

class ContentTypeHandler implements HandlerInterface
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var Queue
     */
    private $solrQueue;

    /**
     * @var SearchContentReferenced
     */
    private $searchContentReferenced;

    /**
     * @var ContentType
     */
    private $contentType;

    /**
     * Constructor.
     *
     * @param DocumentManager         $documentManager
     * @param Queue                   $solrQueue
     * @param SearchContentReferenced $searchContentReferenced
     * @param string                  $contentType
     */
    public function __construct(DocumentManager $documentManager, Queue $solrQueue, SearchContentReferenced $searchContentReferenced, string $contentType)
    {
        $this->documentManager = $documentManager;
        $this->solrQueue = $solrQueue;
        $this->searchContentReferenced = $searchContentReferenced;
        $this->contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContentInterface $content)
    {
        $contentType = $this->documentManager->getRepository(ContentType::class)->find($this->contentType);
        if (!$contentType) {
            return;
        }

        $contentTypeOld = $this->documentManager->getRepository(ContentType::class)->find($content->getContentType());

        if ($contentType->getClass() != $contentTypeOld->getClass()) {
            //don't allow update when item is referenced, because class in reference need to be updated
            $referencedItems = $this->searchContentReferenced->getReferenced($content);
            foreach ($referencedItems as $referencedItem) {
                throw new \Exception('Item '.(string)$content.' is referenced by item '.$referencedItem['id'].' "'.$referencedItem['name'].'" and can\'t be moved to another document type');
            }

            $this->documentManager->getDocumentCollection($contentTypeOld->getClass())->getMongoCollection()->update(['_id' => $content->getId()], ['$set' => ['class' => $contentType->getClass()]]);

            $content = $this->documentManager->getRepository($contentType->getClass())->find($content->getId());
        }

        //remove old document from Solr, to avoid duplicate indexing
        $job = new Job('DELETE');

        $job->setOption('document.id', $contentTypeOld->getId().'-'.$content->getId());

        $job->setOption('document.data', json_encode(['id' => $content->getId()]));
        $job->setOption('document.class', $contentTypeOld->getClass());
        $job->setOption('document.format', 'json');

        $this->solrQueue->push($job);

        $content->setContentType($contentType->getId());
    }
}
