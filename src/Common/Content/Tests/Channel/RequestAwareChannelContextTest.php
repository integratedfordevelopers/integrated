<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Tests\Channel;

use Integrated\Common\Content\Channel\ChannelManagerInterface;
use Integrated\Common\Content\Channel\RequestAwareChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RequestAwareChannelContextTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ChannelManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    /**
     * @var RequestStack|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stack;

    protected function setUp(): void
    {
        $this->manager = $this->createMock('Integrated\\Common\\Content\\Channel\\ChannelManagerInterface');
        $this->stack = $this->createMock('Symfony\\Component\\HttpFoundation\\RequestStack');
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Integrated\\Common\\Content\\Channel\\ChannelContextInterface', $this->getInstance());
    }

    public function testSetChannel()
    {
        $request = new Request();

        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $channel = $this->createMock('Integrated\\Common\\Content\\Channel\\ChannelInterface');
        $channel->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('this-is-the-id');

        $this->getInstance()->setChannel($channel);

        $this->assertTrue($request->attributes->has('_channel'));
        $this->assertEquals('this-is-the-id', $request->attributes->get('_channel'));
    }

    public function testSetChannelAttributeKey()
    {
        $request = new Request();

        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $channel = $this->createMock('Integrated\\Common\\Content\\Channel\\ChannelInterface');
        $channel->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('this-is-the-id');

        $this->getInstance('_the_attribute_key_')->setChannel($channel);

        $this->assertFalse($request->attributes->has('_channel'));
        $this->assertTrue($request->attributes->has('_the_attribute_key_'));
        $this->assertEquals('this-is-the-id', $request->attributes->get('_the_attribute_key_'));
    }

    public function testSetChannelNoRequest()
    {
        $this->stack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $this->getInstance()->setChannel($this->createMock('Integrated\\Common\\Content\\Channel\\ChannelInterface')); // should not return a error
    }

    public function testSetChannelNull()
    {
        $request = new Request();
        $request->attributes->set('_channel', 'should-be-removed');

        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->getInstance()->setChannel();

        $this->assertFalse($request->attributes->has('_channel'));
    }

    public function testGetChannel()
    {
        $request = new Request();
        $request->attributes->set('_channel', 'this-is-the-id');

        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $channel = $this->createMock('Integrated\\Common\\Content\\Channel\\ChannelInterface');

        $this->manager->expects($this->atLeastOnce())
            ->method('find')
            ->with($this->equalTo('this-is-the-id'))
            ->willReturn($channel);

        $this->assertSame($channel, $this->getInstance()->getChannel());
    }

    public function testGetChannelAttributeKey()
    {
        $request = new Request();
        $request->attributes->set('_the_attribute_key_', 'this-is-the-id');

        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $channel = $this->createMock('Integrated\\Common\\Content\\Channel\\ChannelInterface');

        $this->manager->expects($this->atLeastOnce())
            ->method('find')
            ->with($this->equalTo('this-is-the-id'))
            ->willReturn($channel);

        $this->assertSame($channel, $this->getInstance('_the_attribute_key_')->getChannel());
    }

    public function testGetChannelNoRequest()
    {
        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $this->manager->expects($this->never())
            ->method($this->anything());

        $this->assertNull($this->getInstance()->getChannel());
    }

    public function testGetChannelNotSet()
    {
        $request = new Request();

        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->manager->expects($this->never())
            ->method($this->anything());

        $this->assertNull($this->getInstance()->getChannel());
    }

    public function testGetChannelNotFound()
    {
        $request = new Request();
        $request->attributes->set('_channel', 'this-is-the-id');

        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->manager->expects($this->atLeastOnce())
            ->method('find')
            ->with($this->equalTo('this-is-the-id'))
            ->willReturn(null);

        $this->assertNull($this->getInstance()->getChannel());
    }

    /**
     * @param string $attribute
     *
     * @return RequestAwareChannelContext
     */
    protected function getInstance($attribute = null)
    {
        if (null === $attribute) {
            return new RequestAwareChannelContext($this->manager, $this->stack);
        }

        return new RequestAwareChannelContext($this->manager, $this->stack, $attribute);
    }
}
