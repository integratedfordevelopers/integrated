<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\FormConfig\Manager;

use ArrayIterator;
use Integrated\Bundle\ContentBundle\FormConfig\Manager\DefaultAwareIterator;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;
use Integrated\Common\FormConfig\FormConfigIdentifierInterface;
use Integrated\Common\FormConfig\FormConfigInterface;
use stdClass as Object;

class DefaultAwareIteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContentTypeInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $type;

    /**
     * @var FormConfigFactoryInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $factory;

    protected function setUp()
    {
        $this->type = $this->createMock(ContentTypeInterface::class);
        $this->factory = $this->createMock(FormConfigFactoryInterface::class);
    }

    public function testIterator()
    {
        $this->factory->expects($this->never())
            ->method($this->anything());

        $config = [
            $this->getConfig('should be second'),
            $this->getConfig('should be third'),
            $this->getConfig('default'),
            $this->getConfig('default'),
            new Object(),
            $this->getConfig('should be last'),
        ];

        $iterator = new ArrayIterator($config);
        $iterator = new DefaultAwareIterator($this->type, $iterator, $this->factory);

        // only the first default should be place at the front

        $this->assertSame([
            $config[2],
            $config[0],
            $config[1],
            $config[3],
            $config[5],
        ], iterator_to_array($iterator));
    }

    public function testIteratorWithoutDefault()
    {
        $default = $this->createMock(FormConfigEditableInterface::class);

        $this->factory->expects($this->once())
            ->method('create')
            ->with($this->identicalTo($this->type), $this->equalTo('default'))
            ->willReturn($default);

        $config = [
            $this->getConfig('should be second'),
            $this->getConfig('should be third'),
            $this->getConfig('should be last'),
        ];

        $iterator = new ArrayIterator($config);
        $iterator = new DefaultAwareIterator($this->type, $iterator, $this->factory);

        $this->assertSame([
            $default,
            $config[0],
            $config[1],
            $config[2],
        ], iterator_to_array($iterator));
    }

    protected function getConfig(string $key): FormConfigInterface
    {
        $id = $this->createMock(FormConfigIdentifierInterface::class);
        $id->expects($this->any())
            ->method('getKey')
            ->willReturn($key);

        $config = $this->createMock(FormConfigInterface::class);
        $config->expects($this->any())
            ->method('getid')
            ->willReturn($id);

        return $config;
    }
}
