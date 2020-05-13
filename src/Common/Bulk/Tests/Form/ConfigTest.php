<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\Tests\Form;

use Integrated\Common\Bulk\Form\ActionMatcherInterface;
use Integrated\Common\Bulk\Form\Config;
use Integrated\Common\Bulk\Form\ConfigInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ActionMatcherInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $matcher;

    protected function setUp(): void
    {
        $this->matcher = $this->createMock(ActionMatcherInterface::class);
    }

    public function testInterface()
    {
        self::assertInstanceOf(ConfigInterface::class, $this->getInstance());
    }

    public function testGetters()
    {
        $config = $this->getInstance();

        self::assertEquals('handler', $config->getHandler());
        self::assertEquals('name', $config->getName());
        self::assertEquals('type', $config->getType());
        self::assertEquals(['option1', 'option2', 'option3'], $config->getOptions());
        self::assertSame($this->matcher, $config->getMatcher());
    }

    /**
     * @return Config
     */
    protected function getInstance()
    {
        return new Config(
            'handler',
            'name',
            'type',
            ['option1', 'option2', 'option3'],
            $this->matcher
        );
    }
}
