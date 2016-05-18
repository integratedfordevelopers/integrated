<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Indexer;

use Integrated\Common\Solr\Indexer\Job;
use Integrated\Common\Solr\Indexer\JobInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class JobTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf(JobInterface::class, $this->getInstance());
    }

    public function testConstructor()
    {
        $instance = $this->getInstance('action', ['name1' => 'value', 'name2' => 42]);

        self::assertEquals('action', $instance->getAction());
        self::assertSame(['name1' => 'value', 'name2' => '42'], $instance->getOptions());
    }

    public function testSerialize()
    {
        /** @var Job $instance */
        $instance = $this->getInstance();

        $instance->setAction('action');
        $instance->setOption('name', 'value');

        $instance = unserialize(serialize($instance));

        self::assertInstanceOf(Job::class, $instance);
        self::assertEquals('action', $instance->getAction());
        self::assertSame(['name' => 'value'], $instance->getOptions());
    }

    public function testSetGetAction()
    {
        $instance = $this->getInstance();

        self::assertNull($instance->getAction());
        self::assertEquals('action', $instance->setAction('action')->getAction());
        self::assertSame('42', $instance->setAction(42)->getAction());
        self::assertNull($instance->setAction(null)->getAction());
    }

    public function testHasAction()
    {
        $instance = $this->getInstance();

        self::assertFalse($instance->hasAction());
        self::assertTrue($instance->setAction('action')->hasAction());
        self::assertFalse($instance->setAction(null)->hasAction());
    }

    public function testSetGetOption()
    {
        $instance = $this->getInstance();

        self::assertNull($instance->getOption('name'));
        self::assertEquals('value', $instance->setOption('name', 'value')->getOption('name'));
        self::assertEquals('42', $instance->setOption('name', 42)->getOption('name'));
    }

    public function testHasOption()
    {
        $instance = $this->getInstance();

        self::assertFalse($instance->hasOption('name'));
        self::assertTrue($instance->setOption('name', 'value')->hasOption('name'));
        self::assertFalse($instance->removeOption('name')->hasOption('name'));
    }

    public function testRemoveOption()
    {
        $instance = $this->getInstance();

        $instance->setOption('name', 'value');
        $instance->removeOption('name');

        self::assertNull($instance->getOption('name'));
    }

    public function testGetOptions()
    {
        $instance = $this->getInstance();

        self::assertSame([], $instance->getOptions());

        $instance->setOption('name1', 'value');
        $instance->setOption('name2', 'value');

        self::assertSame(['name1' => 'value', 'name2' => 'value'], $instance->getOptions());
    }

    public function testClearOptions()
    {
        $instance = $this->getInstance();

        $instance->setOption('name1', 'value');
        $instance->setOption('name2', 'value');
        $instance->clearOptions();

        self::assertSame([], $instance->getOptions());
    }

    /**
     * @param null  $action
     * @param array $options
     *
     * @return Job
     */
    protected function getInstance($action = null, array $options = [])
    {
        return new Job($action, $options);
    }
}
