<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer;

use Solarium\QueryType\Update\Query\Command\AbstractCommand;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface CommandFactoryInterface
{
    /**
     * Create a solarium update command from a job.
     *
     * @param JobInterface $job
     *
     * @return AbstractCommand
     */
    public function create(JobInterface $job);
}
