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
use Integrated\Common\Queue\Queue;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeHandlerFactory implements HandlerFactoryInterface
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * @var Queue
     */
    private $solrQueue;

    /**
     * @var SearchContentReferenced
     */
    private $searchContentReferenced;

    /**
     * Constructor.
     *
     * @param DocumentManager         $documentManager
     * @param Queue                   $solrQueue
     * @param SearchContentReferenced $searchContentReferenced
     */
    public function __construct(DocumentManager $documentManager, Queue $solrQueue, SearchContentReferenced $searchContentReferenced)
    {
        $this->documentManager = $documentManager;
        $this->solrQueue = $solrQueue;
        $this->searchContentReferenced = $searchContentReferenced;

        $this->resolver = new OptionsResolver();
        $this->resolver
            ->setRequired(['contentType'])
            ->addAllowedTypes('contentType', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function createHandler(array $options)
    {
        $options = $this->resolver->resolve($options);

        return new ContentTypeHandler($this->documentManager, $this->solrQueue, $this->searchContentReferenced, $options['contentType']);
    }
}
