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
     * Constructor.
     *
     * @param DocumentManager         $documentManager         document manager, manage content items
     * @param SearchContentReferenced $searchContentReferenced service to find out if a content item is in use
     *                                                         somewhere - we don't allow a class change when the
     *                                                         content item is referenced
     */
    public function __construct(DocumentManager $documentManager, SearchContentReferenced $searchContentReferenced)
    {
        $this->documentManager = $documentManager;
        $this->searchContentReferenced = $searchContentReferenced;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContentInterface $content)
    {
        $referencedItems = $this->searchContentReferenced->getReferenced($content);
        if (\count($referencedItems) > 0) {
            return;
            throw new \Exception('Item '.(string) $content.' is referenced by '.\count($referencedItems).' other content item(s) and can\'t be deleted');
        }

        $this->documentManager->remove($content);
        $this->documentManager->flush();
    }
}
