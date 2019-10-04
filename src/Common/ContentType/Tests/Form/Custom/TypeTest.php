<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Tests\Form\Custom;

use Integrated\Common\ContentType\Form\Custom\Type;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class TypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Type | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $type;

    /**
     * Setup the test.
     */
    protected function setUp()
    {
        $this->type = new Type();
    }

    /**
     * Test interface of type.
     */
    public function testInterface()
    {
        $this->assertInstanceOf('Integrated\\Common\\ContentType\\Form\\Custom\\TypeInterface', $this->type);
    }

    /**
     * Test get and setType function.
     */
    public function testGetAndSetTypeFunction()
    {
        $type = 'type';
        $this->assertSame($this->type, $this->type->setType($type));
        $this->assertSame($type, $this->type->getType());
    }

    /**
     * Test get and setName function.
     */
    public function testGetAndSetNameFunction()
    {
        $name = 'name';
        $this->assertSame($this->type, $this->type->setName($name));
        $this->assertSame($name, $this->type->getName());
    }
}
