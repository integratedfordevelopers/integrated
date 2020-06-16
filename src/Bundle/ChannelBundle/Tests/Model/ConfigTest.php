<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Tests\Model;

use Integrated\Bundle\ChannelBundle\Model\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->config = new Config(1);
    }

    /**
     * Test default values.
     */
    public function testDefaultValues()
    {
        $this->assertInstanceOf('\DateTime', $this->config->getCreated());
    }

    /**
     * Test get- and setId function.
     */
    public function testGetAndSetIdFunction()
    {
        $this->assertEquals(1, $this->config->getId());
    }

    /**
     * Test get- and setName function.
     */
    public function testGetAndSetNameFunction()
    {
        $name = 'name';
        $this->assertEquals($name, $this->config->setName($name)->getName());
    }

    /**
     * Test get- and setAdapter function.
     */
    public function testGetAndSetAdapterFunction()
    {
        $adapter = 'adapter';
        $this->assertSame($adapter, $this->config->setAdapter($adapter)->getAdapter());
    }

    /**
     * Test get- and setCreated function.
     */
    public function testGetAndSetCreatedFunction()
    {
        $created = new \DateTime();
        $this->assertSame($created, $this->config->setCreated($created)->getCreated());
    }

    /**
     * Test get- and setUpdated function.
     */
    public function testGetAndSetUpdatedFunction()
    {
        $updated = new \DateTime();
        $this->assertSame($updated, $this->config->setUpdated($updated)->getUpdated());
    }
}
