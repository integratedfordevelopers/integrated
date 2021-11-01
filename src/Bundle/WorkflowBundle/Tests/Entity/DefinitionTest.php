<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Tests\Entity;

use Integrated\Bundle\WorkflowBundle\Entity\Definition;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DefinitionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Integrated\Bundle\WorkflowBundle\Entity\Definition\State|\PHPUnit_Framework_MockObject_MockObject
     */
    private $state;

    /**
     * Set up the test.
     */
    protected function setup(): void
    {
        $this->state = $this->createMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');
    }

    /**
     * Test setDefault function.
     *
     * @return Definition
     */
    public function testSetDefault()
    {
        $instance = $this->getInstance();

        $this->assertSame($instance, $instance->setDefault($this->state));

        return $instance;
    }

    /**
     * Test getDefault function.
     *
     * @depends testSetDefault
     *
     * @param Definition $instance
     */
    public function testGetDefault(Definition $instance)
    {
        $this->assertEquals($this->state, $instance->getDefault());
    }

    public function testRemoveDefault()
    {
        $instance = $this->getInstance();

        /** @var \Integrated\Bundle\WorkflowBundle\Entity\Definition\State|\PHPUnit_Framework_MockObject_MockObject $state */
        $state = $this->createMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        // First add the state and then remove it with the setDefault function
        $instance->setDefault($state);
        $instance->setDefault();

        $this->assertNull($instance->getDefault());
    }

    /**
     * @return Definition
     */
    protected function getInstance()
    {
        return new Definition();
    }
}
