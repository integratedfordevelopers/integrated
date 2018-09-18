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
use Integrated\Common\FormConfig\Field\CacheProvider;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;

class CacheProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormConfigFieldProviderInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $provider;

    protected function setUp()
    {
        $this->provider = $this->createMock(FormConfigFieldProviderInterface::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigFieldProviderInterface::class, new CacheProvider($this->provider));
    }

    public function testGetFields()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $type->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('this-is-the-id');

        $fields = [
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
        ];

        $this->provider->expects($this->once())
            ->method('getFields')
            ->with($this->identicalTo($type))
            ->willReturn($fields);

        $provider = new CacheProvider($this->provider);

        $this->assertSame($fields, $provider->getFields($type));
        $this->assertSame($fields, $provider->getFields($type));
    }
}
