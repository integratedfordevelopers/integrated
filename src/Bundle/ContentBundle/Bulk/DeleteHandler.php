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
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Services\SearchContentReferenced;
use Integrated\Common\Bulk\Action\HandlerInterface;
use Integrated\Common\Content\ContentInterface;

/**
 * Handler to delete the content item.
 */
class DeleteHandler implements HandlerInterface
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var SearchContentReferenced
     */
    private $searchContentReferenced;

    /**
     * @var bool
     */
    private $removeReferences;

    /**
     * Constructor.
     *
     * @param DocumentManager         $documentManager         document manager, manage content items
     * @param SearchContentReferenced $searchContentReferenced service to find out if a content item is in use
     *                                                         somewhere - we don't allow a class change when the
     *                                                         content item is referenced
     * @param bool                    $removeReferences
     */
    public function __construct(DocumentManager $documentManager, SearchContentReferenced $searchContentReferenced, ?bool $removeReferences)
    {
        $this->documentManager = $documentManager;
        $this->searchContentReferenced = $searchContentReferenced;
        $this->removeReferences = $removeReferences;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContentInterface $content)
    {
        if ($this->removeReferences === true) {
            $this->documentManager->createQueryBuilder(Content::class)
                ->updateMany()
                ->field('relations.references.$id')->equals($content->getId())
                ->field('relations.$.references')->pull(['$id' => $content->getId()])
                ->getQuery()
                ->execute();
        }

        $referencedItems = $this->searchContentReferenced->getReferenced($content);

        if (\count($referencedItems) > 0) {
            return;
        }

        $this->documentManager->remove($content);
        $this->documentManager->flush();
    }
}
