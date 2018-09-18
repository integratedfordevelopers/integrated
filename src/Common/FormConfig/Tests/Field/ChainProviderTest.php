<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\FormConfig\Tests\Field;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\Field\ChainProvider;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;

class ChainProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormConfigFieldProviderInterface[] | \PHPUnit\Framework\MockObject\MockObject[]
     */
    private $providers = [];

    protected function setUp()
    {
        $this->providers = [
            $this->createMock(FormConfigFieldProviderInterface::class),
            $this->createMock(FormConfigFieldProviderInterface::class),
            $this->createMock(FormConfigFieldProviderInterface::class)
        ];
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigFieldProviderInterface::class, new ChainProvider([]));
    }

    public function testGetFields()
    {
        $type = $this->createMock(ContentTypeInterface::class);

        $fields = [
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
        ];

        $this->providers[0]->expects($this->once())
            ->method('getFields')
            ->with($this->identicalTo($type))
            ->willReturn([$fields[0], $fields[1]]);

        $this->providers[1]->expects($this->once())
            ->method('getFields')
            ->with($this->identicalTo($type))
            ->willReturn([]);

        $this->providers[2]->expects($this->once())
            ->method('getFields')
            ->with($this->identicalTo($type))
            ->willReturn([$fields[2], $fields[3], $fields[4]]);

        $this->assertSame($fields, (new ChainProvider($this->providers))->getFields($type));
    }

    public function testGetFieldNoProviders()
    {
        $this->assertEquals([], (new ChainProvider([]))->getFields($this->createMock(ContentTypeInterface::class)));
    }
}
