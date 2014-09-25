<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Tests\Entity\Definition;

use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setDefault function
     */
    public function testSetDefault()
    {
        $state = $this->getInstance();

        // Set default must return itself
        $this->assertSame($state, $state->setDefault(false));
    }

    /**
     * Test isDefault function
     */
    public function testIsDefault()
    {
        $state = $this->getInstance();

        // Default must be false
        $this->assertFalse($state->isDefault());

        // Set default to true and check if it is
        $this->assertTrue($state->setDefault(true)->isDefault());
    }

    /**
     * @return State
     */
    protected function getInstance()
    {
        $instance = new State();
        return $instance;
    }
}
