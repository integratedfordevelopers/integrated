<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Task\Tasks;

use Integrated\Common\Solr\Task\Tasks\ContentTypeQueueTask;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeQueueTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testGetId()
    {
        self::assertEquals('this-is-the-id', $this->getInstance('this-is-the-id')->getId());
    }

    public function testSerialization()
    {
        $instance = $this->getInstance('this-is-the-id');
        $instance = unserialize(serialize($instance));

        self::assertEquals('this-is-the-id', $instance->getId());
    }

    /**
     * @param string $id
     *
     * @return ContentTypeQueueTask
     */
    protected function getInstance($id)
    {
        return new ContentTypeQueueTask($id);
    }
}
