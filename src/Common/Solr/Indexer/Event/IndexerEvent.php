<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer\Event;

use Integrated\Common\Solr\Indexer\IndexerInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IndexerEvent extends Event
{
    /**
     * @var IndexerInterface
     */
    private $indexer;

    /**
     * Event constructor.
     *
     * @param IndexerInterface $indexer
     */
    public function __construct(IndexerInterface $indexer)
    {
        $this->indexer = $indexer;
    }

    /**
     * Get the indexer object for this event.
     *
     * @return IndexerInterface
     */
    public function getIndexer()
    {
        return $this->indexer;
    }
}
