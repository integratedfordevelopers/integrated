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
use Integrated\Common\Bulk\Action\HandlerFactoryInterface;

class DeleteHandlerFactory implements HandlerFactoryInterface
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
     * @param DocumentManager         $documentManager
     * @param SearchContentReferenced $searchContentReferenced
     */
    public function __construct(DocumentManager $documentManager, SearchContentReferenced $searchContentReferenced)
    {
        $this->documentManager = $documentManager;
        $this->searchContentReferenced = $searchContentReferenced;
    }

    /**
     * {@inheritdoc}
     */
    public function createHandler(array $options)
    {
        return new DeleteHandler($this->documentManager, $this->searchContentReferenced);
    }
}
