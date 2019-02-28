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

/**
 * Handler to change the content type of a content item.
 */
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
     * @param DocumentManager         $documentManager         document manager, manage content items
     * @param Queue                   $solrQueue               solr queue service - a content type change results in
     *                                                         a new Solr ID, so a re-queue is required for the old ID
     * @param SearchContentReferenced $searchContentReferenced service to find out if a content item is in use
     *                                                         somewhere - we don't allow a class change when the
     *                                                         content item is referenced
     * @param string                  $contentType             the new content type for
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
        if (null === $contentType) {
            throw new \Exception('Content type '.$this->contentType.' does not exist');
        }

        $contentTypeOld = $this->documentManager->getRepository(ContentType::class)->find($content->getContentType());
        if (null === $contentTypeOld) {
            throw new \Exception('Content type '.$content->getContentType().' for '.(string) $content.' does not exist');
        }

        if ($contentType->getId() == $contentTypeOld->getId()) {
            //contenttype is already the same
            return;
        }

        if ($contentType->getClass() != $contentTypeOld->getClass()) {
            //don't allow update when item is referenced, because class in reference need to be updated
            $referencedItems = $this->searchContentReferenced->getReferenced($content);
            if (\count($referencedItems) > 0) {
                throw new \Exception('Item '.(string) $content.' is referenced by '.\count($referencedItems).' other content item(s) and can\'t be moved to another document type');
            }

            //update class of content, directly on the database because the documentManager doesn't support class updates
            $this->documentManager->getDocumentCollection($contentTypeOld->getClass())->getMongoCollection()->update(['_id' => $content->getId()], ['$set' => ['class' => $contentType->getClass()]]);

            $content = $this->documentManager->getRepository($contentType->getClass())->find($content->getId());
        }

        //remove old document from Solr, to avoid duplicate indexing
        $this->deleteFromSolr($contentTypeOld, $content);

        $content->setContentType($contentType->getId());
    }

    /**
     * @param ContentType      $contentType
     * @param ContentInterface $contentItem
     */
    private function deleteFromSolr(ContentType $contentType, ContentInterface $contentItem)
    {
        $job = new Job('DELETE');

        $job->setOption('document.id', $contentType->getId().'-'.$contentItem->getId());

        $job->setOption('document.data', json_encode(['id' => $contentItem->getId()]));
        $job->setOption('document.class', $contentType->getClass());
        $job->setOption('document.format', 'json');

        $this->solrQueue->push($job);
    }
}
