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

use Integrated\Bundle\ContentBundle\FormConfig\Factory\DefaultNameSetterFactory;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;

class DefaultNameSetterFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormConfigFactoryInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = $this->createMock(FormConfigFactoryInterface::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigFactoryInterface::class, new DefaultNameSetterFactory($this->factory, 'name'));
    }

    public function testCreate()
    {
        $config = $this->createMock(FormConfigEditableInterface::class);
        $config->expects($this->atLeastOnce())
            ->method('setName')
            ->with($this->equalTo('name'));

        $type = $this->createMock(ContentTypeInterface::class);

        $this->factory->expects($this->once())
            ->method('create')
            ->with($this->identicalTo($type), $this->equalTo('key'))
            ->willReturn($config);

        $this->assertSame($config, (new DefaultNameSetterFactory($this->factory, 'name'))->create($type, 'key'));
    }
}
