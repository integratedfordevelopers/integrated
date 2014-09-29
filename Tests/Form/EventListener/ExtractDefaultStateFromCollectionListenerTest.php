<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Tests\Form\EventListener;

use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Form\EventListener\ExtractDefaultStateFromCollectionListener;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ExtractDefaultStateFromCollectionListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $form;

    /**
     * @var \Symfony\Component\Form\FormEvent | \PHPUnit_Framework_MockObject_MockObject
     */
    private $event;

    /**
     * @var Definition
     */
    private $definition;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->event = $this->getMock('Symfony\Component\Form\FormEvent', [], [], '', false);
        $this->form = $this->getMock('Symfony\Component\Form\FormInterface');
        $this->definition = new Definition();
    }

    /**
     * Test instance of
     */
    public function testInstanceOf()
    {
        $instance = $this->getInstance();
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $instance);
    }

    /**
     * Test getSubscribedEvents function
     */
    public function testGetSubscribedEvents()
    {
        $instance = $this->getInstance();
        $this->assertCount(3, $instance::getSubscribedEvents());
    }

    /**
     * Test onPreSubmit event
     */
    public function testOnPreSubmit()
    {
        $instance = $this->getInstance();

        // Stub getForm function
        $this->event
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($this->form))
        ;

        // Stub getData function
        $this->form
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($this->definition))
        ;

        /** @var \Integrated\Bundle\WorkflowBundle\Entity\Definition\State | \PHPUnit_Framework_MockObject_MockObject $state */
        $state = $this->getMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        // Set default state
        $this->definition->setDefault($state);

        // Fire event
        $instance->onPreSubmit($this->event);

        // Asserts
        $this->assertNull($this->definition->getDefault());
    }

    /**
     * Test onPostSetData function
     */
    public function testOnPostSetData()
    {
        // TODO test onPostSetData function
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * Test onPostSubmit function
     */
    public function testOnPostSubmit()
    {
        // TODO test onPostSubmit function
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @return ExtractDefaultStateFromCollectionListener
     */
    protected function getInstance()
    {
        $instance = new ExtractDefaultStateFromCollectionListener();
        return $instance;
    }
}
