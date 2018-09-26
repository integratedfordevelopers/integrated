<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\FormConfig;

use Integrated\Bundle\ContentBundle\FormConfig\Handler;
use Integrated\Bundle\ContentBundle\FormConfig\Util\KeyGenerator;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\Exception\InvalidArgumentException;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Integrated\Common\FormConfig\FormConfigInterface;
use Integrated\Common\FormConfig\FormConfigManagerInterface;

class HandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormConfigManagerInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $manager;

    /**
     * @var FormConfigFactoryInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $factory;

    /**
     * @var KeyGenerator | \PHPUnit\Framework\MockObject\MockObject
     */
    private $generator;

    protected function setUp()
    {
        $this->manager = $this->createMock(FormConfigManagerInterface::class);
        $this->factory = $this->createMock(FormConfigFactoryInterface::class);
        $this->generator = $this->createMock(KeyGenerator::class);
    }

    public function testHandle()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $data = [
            'name' => 'the-name',
            'fields' => [
                $this->createMock(FormConfigFieldInterface::class),
                $this->createMock(FormConfigFieldInterface::class),
                $this->createMock(FormConfigFieldInterface::class),
            ],
        ];

        $this->manager->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($type), $this->equalTo('key'))
            ->willReturn(true);

        $config = $this->createMock(FormConfigEditableInterface::class);
        $config->expects($this->once())
            ->method('setName')
            ->with($this->equalTo($data['name']));

        $config->expects($this->once())
            ->method('setFields')
            ->with($this->identicalTo($data['fields']));

        $this->manager->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($type), $this->equalTo('key'))
            ->willReturn($config);

        $this->factory->expects($this->never())
            ->method($this->anything());

        (new Handler($this->manager, $this->factory))->handle($type, 'key', $data);
    }

    public function testHandleGenerateKey()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $data = [
            'name' => 'the-name',
            'fields' => [
                $this->createMock(FormConfigFieldInterface::class),
                $this->createMock(FormConfigFieldInterface::class),
                $this->createMock(FormConfigFieldInterface::class),
            ],
        ];

        $this->generator->expects($this->once())
            ->method('generate')
            ->with($this->equalTo($data['name']))
            ->willReturn('the_name');

        $this->manager->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($type), $this->equalTo('the_name'))
            ->willReturn(true);

        $config = $this->createMock(FormConfigEditableInterface::class);
        $config->expects($this->once())
            ->method('setName')
            ->with($this->equalTo($data['name']));

        $config->expects($this->once())
            ->method('setFields')
            ->with($this->identicalTo($data['fields']));

        $this->manager->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($type), $this->equalTo('the_name'))
            ->willReturn($config);

        $this->factory->expects($this->never())
            ->method($this->anything());

        (new Handler($this->manager, $this->factory, $this->generator))->handle($type, null, $data);
    }

    public function testHandleCreateNew()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $data = [
            'name' => 'the-name',
            'fields' => [
                $this->createMock(FormConfigFieldInterface::class),
                $this->createMock(FormConfigFieldInterface::class),
                $this->createMock(FormConfigFieldInterface::class),
            ],
        ];

        $this->manager->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($type), $this->equalTo('key'))
            ->willReturn(false);

        $config = $this->createMock(FormConfigEditableInterface::class);
        $config->expects($this->once())
            ->method('setName')
            ->with($this->equalTo($data['name']));

        $config->expects($this->once())
            ->method('setFields')
            ->with($this->identicalTo($data['fields']));

        $this->factory->expects($this->once())
            ->method('create')
            ->with($this->identicalTo($type), $this->equalTo('key'))
            ->willReturn($config);

        (new Handler($this->manager, $this->factory))->handle($type, 'key', $data);
    }

    public function testHandleNotEditable()
    {
        $this->expectException(InvalidArgumentException::class);

        $type = $this->createMock(ContentTypeInterface::class);
        $data = [
            'name' => 'the-name',
            'fields' => [
                $this->createMock(FormConfigFieldInterface::class),
                $this->createMock(FormConfigFieldInterface::class),
                $this->createMock(FormConfigFieldInterface::class),
            ],
        ];

        $this->manager->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($type), $this->equalTo('key'))
            ->willReturn(true);

        $config = $this->createMock(FormConfigInterface::class);

        $this->manager->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($type), $this->equalTo('key'))
            ->willReturn($config);

        $this->factory->expects($this->never())
            ->method($this->anything());

        (new Handler($this->manager, $this->factory))->handle($type, 'key', $data);
    }
}
