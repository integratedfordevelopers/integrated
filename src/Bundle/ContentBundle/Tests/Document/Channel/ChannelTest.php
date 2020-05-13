<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Channel;

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ChannelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->channel = new Channel();
    }

    public function testDefaultValues()
    {
        $this->assertInstanceOf('\DateTime', $this->channel->getCreatedAt());
    }

    /**
     * Test get- and setId function.
     */
    public function testGetAndSetIdFunction()
    {
        $id = 'id';
        $this->assertSame($id, $this->channel->setId($id)->getId());
    }

    /**
     * Test get- and setName function.
     */
    public function testGetAndSetNameFunction()
    {
        $name = 'name';
        $this->assertEquals($name, $this->channel->setName($name)->getName());
    }

    /**
     * Test get- and setDomains function.
     */
    public function testGetAndSetDomainsFunction()
    {
        $domains = [
            'domain1',
            'domain2',
        ];

        $this->assertSame($domains, $this->channel->setDomains($domains)->getDomains());
    }

    /**
     * Test get- and setCreatedAt function.
     */
    public function testGetAndSetCreatedAtFunction()
    {
        $createdAt = new \DateTime();
        $this->assertSame($createdAt, $this->channel->setCreatedAt($createdAt)->getCreatedAt());
    }
}
