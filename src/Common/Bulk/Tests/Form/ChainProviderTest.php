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

use Integrated\Common\Bulk\Form\ChainProvider;
use Integrated\Common\Bulk\Form\ConfigProviderInterface;
use Integrated\Common\Content\ContentInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChainProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConfigProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $provider1;

    /**
     * @var ConfigProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $provider2;

    protected function setUp(): void
    {
        $this->provider1 = $this->createMock(ConfigProviderInterface::class);
        $this->provider2 = $this->createMock(ConfigProviderInterface::class);
    }

    public function testInterface()
    {
        self::assertInstanceOf(ConfigProviderInterface::class, $this->getInstance());
    }

    public function testGetConfig()
    {
        $content = [
            $this->createMock(ContentInterface::class),
            $this->createMock(ContentInterface::class),
        ];

        $config = [
            new stdClass(),
            new stdClass(),
            new stdClass(),
            new stdClass(),
            new stdClass(),
        ];

        $this->provider1->expects($this->once())
            ->method('getConfig')
            ->with($this->identicalTo($content))
            ->willReturn([$config[0], $config[1], $config[2]]);

        $this->provider2->expects($this->once())
            ->method('getConfig')
            ->with($this->identicalTo($content))
            ->willReturn([$config[3], $config[4]]);

        self::assertSame($config, $this->getInstance()->getConfig($content));
    }

    /**
     * @return ChainProvider
     */
    protected function getInstance()
    {
        return new ChainProvider([$this->provider1, $this->provider2]);
    }
}
