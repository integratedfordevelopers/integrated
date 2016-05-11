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

use Integrated\Common\Solr\Indexer\BatchOperation;
use Integrated\Common\Queue\QueueMessageInterface;

use Solarium\QueryType\Update\Query\Command\Command;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BatchOperationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueueMessageInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $message;

    /**
     * @var Command | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $command;

    protected function setUp()
    {
        $this->message = $this->getMock(QueueMessageInterface::class);
        $this->command = $this->getMock(Command::class);
    }

    public function testConstructorNullCommand()
    {
        $this->command = null;
        self::assertNull($this->getInstance()->getCommand());
    }

    public function testGetMessage()
    {
        self::assertSame($this->message, $this->getInstance()->getMessage());
    }

    public function testGetCommand()
    {
        self::assertSame($this->command, $this->getInstance()->getCommand());
    }

    public function testSetCommand()
    {
        $command = $this->getMock(Command::class);

        $instance = $this->getInstance();
        $instance->setCommand($command);

        self::assertSame($command, $instance->getCommand());
    }

    public function testSetCommandNull()
    {
        $instance = $this->getInstance();
        $instance->setCommand(null);

        self::assertNull($instance->getCommand());
    }

    /**
     * @return BatchOperation
     */
    protected function getInstance()
    {
        return new BatchOperation($this->message, $this->command);
    }
}
