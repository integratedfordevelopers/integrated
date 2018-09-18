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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Integrated\Bundle\ContentBundle\FormConfig\Factory\PersistFactory;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;

class PersistFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormConfigFactoryInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $factory;

    /**
     * @var ManagerRegistry | \PHPUnit\Framework\MockObject\MockObject
     */
    private $registry;

    protected function setUp()
    {
        $this->factory = $this->createMock(FormConfigFactoryInterface::class);
        $this->registry = $this->createMock(ManagerRegistry::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigFactoryInterface::class, new PersistFactory($this->factory, $this->registry));
    }

    public function testCreate()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $config = $this->createMock(FormConfigEditableInterface::class);

        $this->factory->expects($this->once())
            ->method('create')
            ->with($this->identicalTo($type), $this->equalTo('key'))
            ->willReturn($config);

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($config));

        $this->registry->expects($this->once())
            ->method('getManagerForClass')
            ->with($this->equalTo(get_class($config)))
            ->willReturn($manager);

        $this->assertSame($config, (new PersistFactory($this->factory, $this->registry))->create($type, 'key'));
    }

    public function testCreateNoManagerFound()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $config = $this->createMock(FormConfigEditableInterface::class);

        $this->factory->expects($this->once())
            ->method('create')
            ->with($this->identicalTo($type), $this->equalTo('key'))
            ->willReturn($config);

        $this->registry->expects($this->once())
            ->method('getManagerForClass')
            ->with($this->equalTo(get_class($config)))
            ->willReturn(null);

        $this->assertSame($config, (new PersistFactory($this->factory, $this->registry))->create($type, 'key'));
    }
}
