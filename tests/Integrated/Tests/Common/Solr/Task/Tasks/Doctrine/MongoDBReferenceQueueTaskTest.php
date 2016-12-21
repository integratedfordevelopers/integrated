<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Task\Tasks\Doctrine;

use Integrated\Common\Solr\Task\Tasks\Doctrine\MongoDBReferenceQueueTask;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MongoDBReferenceQueueTaskTest extends \Integrated\Tests\Common\Solr\Task\Tasks\ReferenceQueueTaskTest
{
    protected function getInstance($id)
    {
        return new MongoDBReferenceQueueTask($id);
    }
}
