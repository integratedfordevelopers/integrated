<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Content\Channel;

use Integrated\Common\Content\Channel\ChannelContext;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelContextTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf('Integrated\\Common\\Content\\Channel\\ChannelContextInterface', $this->getInstance());
    }

    public function testSetGetChannel()
    {
        $channel = $this->createMock('Integrated\\Common\\Content\\Channel\\ChannelInterface');
        $instance = $this->getInstance();

        $this->assertNull($instance->getChannel());

        $instance->setChannel($channel);

        $this->assertSame($channel, $instance->getChannel());

        $instance->setChannel();

        $this->assertNull($instance->getChannel());
    }

    /**
     * @return ChannelContext
     */
    protected function getInstance()
    {
        return new ChannelContext();
    }
}
