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
     * Test get- and setDefault function
     */
    public function testGetAndSetDefault()
    {
        $instance = $this->getInstance();

        /** @var \Integrated\Bundle\WorkflowBundle\Entity\Definition\State | \PHPUnit_Framework_MockObject_MockObject $state */
        $state = $this->getMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');


        $this->assertSame($instance, $instance->setDefault($state));
        $this->assertSame($state, $instance->getDefault());
    }

    public function testRemoveDefault()
    {
        $instance = $this->getInstance();

        /** @var \Integrated\Bundle\WorkflowBundle\Entity\Definition\State | \PHPUnit_Framework_MockObject_MockObject $state */
        $state = $this->getMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

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
