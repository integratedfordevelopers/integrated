<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Tests\Exporter;

use Exception;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Exporter\ExporterInterface;
use Integrated\Common\Channel\Exporter\Queue\Request;
use Integrated\Common\Channel\Exporter\Queue\RequestSerializerInterface;
use Integrated\Common\Channel\Exporter\QueueExporter;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Queue\QueueMessageInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueExporterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    public const TEST_STATE = 'TEST';

    /**
     * @var QueueInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queue;

    /**
     * @var RequestSerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;

    /**
     * @var ExporterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $exporter;

    protected function setUp(): void
    {
        $this->queue = $this->createMock('Integrated\\Common\\Queue\\QueueInterface');
        $this->serializer = $this->createMock('Integrated\\Common\\Channel\\Exporter\\Queue\\RequestSerializerInterface');
        $this->exporter = $this->createMock('Integrated\\Common\\Channel\\Exporter\\ExporterInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Exporter\\ExporterInterface', $this->getInstance());
    }

    public function testGetQueue()
    {
        self::assertSame($this->queue, $this->getInstance()->getQueue());
    }

    public function testGetSerializer()
    {
        self::assertSame($this->serializer, $this->getInstance()->getSerializer());
    }

    public function testGetExporter()
    {
        self::assertSame($this->exporter, $this->getInstance()->getExporter());
    }

    public function testExecute()
    {
        $message1 = $this->getMessage();
        $message1->expects($this->once())
            ->method('delete');

        $message2 = $this->getMessage();
        $message2->expects($this->once())
            ->method('delete');

        $message3 = $this->getMessage();
        $message3->expects($this->once())
            ->method('delete');

        $this->queue->expects($this->once())
            ->method('pull')
            ->with($this->equalTo(1000))
            ->willReturn([$message1, $message2, $message3]);

        $this->serializer->expects($this->never())
            ->method($this->anything());

        $this->exporter->expects($this->never())
            ->method($this->anything());

        $exporter = $this->getInstance('process');
        $exporter->expects($this->exactly(3))
            ->method('process')
            ->withConsecutive([$this->identicalTo($message1)], [$this->identicalTo($message2)], [$this->identicalTo($message3)])
            ->willReturnArgument(0);

        $exporter->execute();
    }

    public function testProcess()
    {
        $message = $this->getMessage();
        $message->expects($this->once())
            ->method('getPayload')
            ->willReturn('valid');

        $request = new Request();

        $request->content = new stdClass();
        $request->state = self::TEST_STATE;
        $request->channel = $this->getChannel();

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($this->equalTo('valid'))
            ->willReturn($request);

        // export calls need to be proxied by the queue exporter export method and since that one is mocked
        // away the proxied exporter should not be called.

        $this->exporter->expects($this->never())
            ->method($this->anything());

        $exporter = $this->getInstance('export');
        $exporter->expects($this->once())
            ->method('export')
            ->with($this->identicalTo($request->content), $this->equalTo(self::TEST_STATE), $this->identicalTo($request->channel));

        self::assertSame($message, $exporter->process($message));
    }

    public function testProcessExportError()
    {
        $message = $this->getMessage();
        $message->expects($this->once())
            ->method('getPayload')
            ->willReturn('valid');

        $request = new Request();

        $request->content = new stdClass();
        $request->state = self::TEST_STATE;
        $request->channel = $this->getChannel();

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($this->equalTo('valid'))
            ->willReturn($request);

        $this->exporter->expects($this->once())
            ->method('export')
            ->with($this->identicalTo($request->content), $this->equalTo(self::TEST_STATE), $this->identicalTo($request->channel))
            ->willThrowException(new Exception('i-will-be-caught-and-not-cause-any-troubles'));

        self::assertSame($message, $this->getInstance()->process($message));
    }

    public function testProcessInvalidRequest()
    {
        $message = $this->getMessage();
        $message->expects($this->once())
            ->method('getPayload')
            ->willReturn('invalid');

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($this->equalTo('invalid'))
            ->willReturn(null);

        $this->exporter->expects($this->never())
            ->method('export');

        self::assertSame($message, $this->getInstance()->process($message));
    }

    public function testExport()
    {
        $content = new stdClass();
        $channel = $this->getChannel();

        $this->exporter->expects($this->once())
            ->method('export')
            ->with($this->identicalTo($content), $this->equalTo(self::TEST_STATE), $this->identicalTo($channel));

        $this->getInstance()->export($content, self::TEST_STATE, $channel);
    }

    /**
     * @return QueueExporter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getInstance($method = null)
    {
        return $this->getMockBuilder('Integrated\\Common\\Channel\\Exporter\\QueueExporter')
            ->setConstructorArgs([$this->queue, $this->serializer, $this->exporter])
            ->onlyMethods($method ? [$method] : [])
            ->getMock();
    }

    /**
     * @return QueueMessageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMessage()
    {
        return $this->createMock('Integrated\\Common\\Queue\\QueueMessageInterface');
    }

    /**
     * @return ChannelInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getChannel()
    {
        return $this->createMock('Integrated\\Common\\Channel\\ChannelInterface');
    }
}
