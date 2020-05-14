<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\Tests;

use Integrated\Common\Bulk\Action\HandlerFactoryInterface;
use Integrated\Common\Bulk\Action\HandlerFactoryRegistry;
use Integrated\Common\Bulk\Action\HandlerInterface;
use Integrated\Common\Bulk\BulkActionInterface;
use Integrated\Common\Bulk\BulkHandler;
use Integrated\Common\Bulk\BulkHandlerInterface;
use Integrated\Common\Bulk\Exception\InvalidArgumentException;
use Integrated\Common\Content\ContentInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BulkHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var HandlerFactoryRegistry | \PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    protected function setUp(): void
    {
        $this->registry = $this->getMockBuilder(HandlerFactoryRegistry::class)->disableOriginalConstructor()->getMock();
    }

    public function testInterface()
    {
        self::assertInstanceOf(BulkHandlerInterface::class, $this->getInstance());
    }

    public function testExecute()
    {
        $content1 = $this->getContent();
        $content2 = $this->getContent();

        $handler1 = $this->getHandler();
        $handler2 = $this->getHandler();
        $handler3 = $this->getHandler();

        $handler1->expects($this->exactly(2))
            ->method('execute')
            ->withConsecutive([$this->identicalTo($content1)], [$this->identicalTo($content2)]);

        $handler2->expects($this->exactly(2))
            ->method('execute')
            ->withConsecutive([$this->identicalTo($content1)], [$this->identicalTo($content2)]);

        $handler3->expects($this->exactly(2))
            ->method('execute')
            ->withConsecutive([$this->identicalTo($content1)], [$this->identicalTo($content2)]);

        $factory1 = $this->getFactory();
        $factory2 = $this->getFactory();

        $factory1->expects($this->exactly(2))
            ->method('createHandler')
            ->withConsecutive([['options1']], [['options2']])
            ->willReturnOnConsecutiveCalls($handler1, $handler2);

        $factory2->expects($this->once())
            ->method('createHandler')
            ->with(['options3'])
            ->willReturn($handler3);

        $this->registry->expects($this->exactly(3))
            ->method('getFactory')
            ->withConsecutive(['class1'], ['class2'], ['class3'])
            ->willReturnOnConsecutiveCalls($factory1, $factory1, $factory2);

        $action1 = $this->getAction('class1', ['options1']);
        $action2 = $this->getAction('class2', ['options2']);
        $action3 = $this->getAction('class3', ['options3']);

        $this->getInstance()->execute([$content1, $content2], [$action1, $action2, $action3]);
    }

    public function testExecuteInvalidContent()
    {
        $this->expectException(\Integrated\Common\Bulk\Exception\UnexpectedTypeException::class);

        $this->getInstance()->execute('not a array or iterator', []);
    }

    public function testExecuteInvalidContentClass()
    {
        $this->expectException(\Integrated\Common\Bulk\Exception\UnexpectedTypeException::class);

        $this->getInstance()->execute([new stdClass()], []);
    }

    public function testExecuteInvalidActions()
    {
        $this->expectException(\Integrated\Common\Bulk\Exception\UnexpectedTypeException::class);

        $this->getInstance()->execute([], 'not a array or iterator');
    }

    public function testExecuteInvalidActionClass()
    {
        $this->expectException(\Integrated\Common\Bulk\Exception\UnexpectedTypeException::class);

        $this->getInstance()->execute([], [new stdClass()]);
    }

    public function testExecuteActionNotFound()
    {
        $this->expectException(\Integrated\Common\Bulk\Exception\ExceptionInterface::class);

        $this->registry->expects($this->once())
            ->method('getFactory')
            ->with('this-does-not-exist')
            ->willThrowException(new InvalidArgumentException());

        $this->getInstance()->execute([], [$this->getAction('this-does-not-exist')]);
    }

    /**
     * @return BulkHandler
     */
    protected function getInstance()
    {
        return new BulkHandler($this->registry);
    }

    /**
     * @return ContentInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContent()
    {
        return $this->createMock(ContentInterface::class);
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return BulkActionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAction($name, array $options = [])
    {
        $mock = $this->createMock(BulkActionInterface::class);

        $mock->expects($this->any())
            ->method('getHandler')
            ->willReturn($name);

        $mock->expects($this->any())
            ->method('getOptions')
            ->willReturn($options);

        return $mock;
    }

    /**
     * @return HandlerFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFactory()
    {
        return $this->createMock(HandlerFactoryInterface::class);
    }

    /**
     * @return HandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getHandler()
    {
        return $this->createMock(HandlerInterface::class);
    }
}
