<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Tests\Exporter\Queue;

use Exception;
use Integrated\Common\Channel\ChannelManagerInterface;
use Integrated\Common\Channel\Exporter\Queue\Request;
use Integrated\Common\Channel\Exporter\Queue\RequestSerializer;
use Integrated\Common\Content\Channel\ChannelInterface;
use stdClass;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RequestSerializerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    const TEST_STATE = 'TEST';

    /**
     * @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializer;

    /**
     * @var ChannelManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock('Symfony\\Component\\Serializer\\SerializerInterface');
        $this->manager = $this->createMock('Integrated\\Common\\Channel\\ChannelManagerInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Exporter\\Queue\\RequestSerializerInterface', $this->getInstance());
    }

    public function testSerialize()
    {
        $request = new Request();

        $request->content = new stdClass();
        $request->state = self::TEST_STATE;
        $request->channel = $this->getChannel('channel');

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($this->identicalTo($request->content), $this->equalTo('json'))
            ->willReturn('serialized-data');

        self::assertEquals($this->getSerialized(), $this->getInstance()->serialize($request));
    }

    public function testSerializeInvalidChannel()
    {
        $request = new Request();

        $request->content = new stdClass();
        $request->state = self::TEST_STATE;
        $request->channel = new stdClass();

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($this->identicalTo($request->content), $this->equalTo('json'))
            ->willReturn('serialized-data');

        self::assertEquals($this->getSerialized(['channel' => null]), $this->getInstance()->serialize($request));
    }

    public function testDeserialize()
    {
        $content = new stdClass();
        $channel = $this->getChannel('channel');

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($this->equalTo('serialized-data'), $this->equalTo('stdClass'), $this->equalTo('json'))
            ->willReturn($content);

        $this->manager->expects($this->once())
            ->method('find')
            ->with($this->equalTo('channel'))
            ->willReturn($channel);

        $request = $this->getInstance()->deserialize($this->getSerialized());

        self::assertSame($content, $request->content);
        self::assertEquals(self::TEST_STATE, $request->state);
        self::assertSame($channel, $request->channel);
    }

    public function testDeserializeInvalidData()
    {
        $this->serializer->expects($this->never())
            ->method($this->anything());

        $this->manager->expects($this->never())
            ->method($this->anything());

        $exporter = $this->getInstance();

        self::assertNull($exporter->deserialize(''));
        self::assertNull($exporter->deserialize('[]'));
        self::assertNull($exporter->deserialize($this->getSerialized(['content' => []])));
        self::assertNull($exporter->deserialize($this->getSerialized(['content' => 'invalid'])));
        self::assertNull($exporter->deserialize($this->getSerialized(['state' => ''])));
        self::assertNull($exporter->deserialize($this->getSerialized(['channel' => ''])));
    }

    public function testDeserializeInvalidContent()
    {
        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->willThrowException(new Exception('i-will-be-caught-and-not-cause-any-troubles'));

        self::assertNull($this->getInstance()->deserialize($this->getSerialized()));
    }

    public function testDeserializeInvalidChannel()
    {
        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->willReturn(new stdClass());

        $this->manager->expects($this->once())
            ->method('find')
            ->willReturn(new stdClass());

        self::assertNull($this->getInstance()->deserialize($this->getSerialized()));
    }

    /**
     * @return RequestSerializer
     */
    protected function getInstance()
    {
        return new RequestSerializer($this->serializer, $this->manager);
    }

    /**
     * @param string $id
     *
     * @return ChannelInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getChannel($id)
    {
        $mock = $this->createMock('Integrated\\Common\\Channel\\ChannelInterface');
        $mock->expects($this->any())
            ->method('getId')
            ->willReturn($id);

        return $mock;
    }

    /**
     * @param array $overwrite
     *
     * @return string
     */
    public function getSerialized(array $overwrite = [])
    {
        $data = [
            'content' => [
                'data' => 'serialized-data',
                'type' => 'stdClass',
            ],
            'state' => self::TEST_STATE,
            'channel' => 'channel',
        ];

        return json_encode(array_merge($data, $overwrite));
    }
}
