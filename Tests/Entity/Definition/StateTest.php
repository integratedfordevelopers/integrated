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

use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test isDefault function
     */
    public function testIsDefault()
    {
        $state1 = $this->getInstance();
        $state2 = $this->getInstance();

        $definition = new Definition();
        $state1->setWorkflow($definition);
        $state2->setWorkflow($definition);

        $definition->setDefault($state1);

        $this->assertTrue($state1->isDefault());
        $this->assertFalse($state2->isDefault());
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
