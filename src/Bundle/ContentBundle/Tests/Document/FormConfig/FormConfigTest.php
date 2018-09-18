<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\FormConfig;

use ArrayIterator;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Identifier;
use Integrated\Bundle\ContentBundle\Document\FormConfig\FormConfig;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use ReflectionProperty;

class FormConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Identifier
     */
    private $identifier;

    /**
     * @var FormConfig
     */
    private $config;

    protected function setUp()
    {
        $this->identifier = new Identifier('content_type', 'key');
        $this->config = new FormConfig($this->identifier);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigEditableInterface::class, $this->config);
    }

    public function testGetId()
    {
        $this->assertSame($this->identifier, $this->config->getId());
    }

    /**
     * The idInstance property of the modal is not managed and should be created on
     * the fly by the modal when required.
     */
    public function testGetIdDoctrineHydration()
    {
        $property = new ReflectionProperty($this->config, 'idInstance');
        $property->setAccessible(true);
        $property->setValue($this->config, null);

        $this->assertInstanceOf(Identifier::class, $this->config->getId());
    }

    public function testGetAndSetName()
    {
        $this->assertEquals('', $this->config->getName());

        $this->config->setName('name');

        $this->assertEquals('name', $this->config->getName());
    }

    public function testGetAndSetFields()
    {
        $this->assertEquals([], $this->config->getFields());

        $fields = [
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
        ];

        $this->config->setFields($fields);

        $this->assertSame($fields, $this->config->getFields());

        $fields = [
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
            $this->createMock(FormConfigFieldInterface::class),
        ];

        $this->config->setFields(new ArrayIterator($fields));

        $this->assertSame($fields, $this->config->getFields());
    }
}
