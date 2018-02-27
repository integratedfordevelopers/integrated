<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Tests\Indexer\Event;

use Integrated\Common\Solr\Indexer\Event\IndexerEvent;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IndexerEventTest extends AbstractEventTest
{
    protected function getInstance()
    {
        return new IndexerEvent($this->indexer);
    }
}
