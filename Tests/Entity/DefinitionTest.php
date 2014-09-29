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
class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setDefault function
     */
    public function testSetDefault()
    {
        // Get instance
        $instance = $this->getInstance();

        // Mock State
        $state = $this->getMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        // Asserts
        $this->assertSame($instance, $instance->setDefault($state));
    }

    /**
     * Test getDefault function
     */
    public function testGetDefault()
    {
        // Get instance
        $instance = $this->getInstance();

        // Mock State
        $state = $this->getMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        // Set default
        $instance->setDefault($state);

        // Asserts
        $this->assertSame($state, $instance->getDefault());
    }

    public function testRemoveDefault()
    {
        // Get instance
        $instance = $this->getInstance();

        // Mock State
        $state = $this->getMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        // Set default
        $instance->setDefault($state);

        // Asserts
        $this->assertSame($instance, $instance->removeDefault());
        $this->assertNull($instance->getDefault());
    }

    /**
     * @return Definition
     */
    protected function getInstance()
    {
        $instance = new Definition();
        return $instance;
    }
}
