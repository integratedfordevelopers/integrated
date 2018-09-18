<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\ContentBundle\Tests\FormConfig\Factory;

use Integrated\Bundle\ContentBundle\FormConfig\Factory\DefaultFieldSetterFactory;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;

class DefaultFieldSetterFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormConfigFactoryInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $factory;

    /**
     * @var FormConfigFieldProviderInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $provider;

    protected function setUp()
    {
        $this->factory = $this->createMock(FormConfigFactoryInterface::class);
        $this->provider = $this->createMock(FormConfigFieldProviderInterface::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigFactoryInterface::class, new DefaultFieldSetterFactory($this->factory, $this->provider));
    }

    public function testCreate()
    {
        $fields = [
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
        ];

        $config = $this->createMock(FormConfigEditableInterface::class);
        $config->expects($this->atLeastOnce())
            ->method('setFields')
            ->with($this->identicalTo($fields));

        $type = $this->createMock(ContentTypeInterface::class);

        $this->factory->expects($this->once())
            ->method('create')
            ->with($this->identicalTo($type), $this->equalTo('key'))
            ->willReturn($config);

        $this->provider->expects($this->once())
            ->method('getFields')
            ->with($this->identicalTo($type))
            ->willReturn($fields);

        $this->assertSame($config, (new DefaultFieldSetterFactory($this->factory, $this->provider))->create($type, 'key'));
    }
}
