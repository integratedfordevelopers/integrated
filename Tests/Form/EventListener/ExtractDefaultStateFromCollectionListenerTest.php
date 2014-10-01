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

use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;
use Integrated\Bundle\WorkflowBundle\Form\EventListener\ExtractDefaultStateFromCollectionListener;

use Symfony\Component\Form\FormEvents;

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
     * @var \Integrated\Bundle\WorkflowBundle\Entity\Definition | \PHPUnit_Framework_MockObject_MockObject
     */
    private $definition;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->event = $this->getMock('Symfony\Component\Form\FormEvent', [], [], '', false);
        $this->form = $this->getMock('Symfony\Component\Form\FormInterface');
        $this->definition = $this->getMock('Integrated\Bundle\WorkflowBundle\Entity\Definition');

        // Stub getForm, returns $this->form
        $this->event
            ->expects($this->any())
            ->method('getForm')
            ->willReturn($this->form)
        ;
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

        $events = array(
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::POST_SET_DATA => 'onPostSetData',
            FormEvents::SUBMIT => 'onSubmit'
        );

        $this->assertSame($events, $instance::getSubscribedEvents());
    }

    /**
     * Test onPreSubmit event
     */
    public function testOnPreSubmit()
    {
        $instance = $this->getInstance();

        // Stub getData, returns $this->definition
        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->definition)
        ;

        // Stub setDefault, called once with null
        $this->definition
            ->expects($this->once())
            ->method('setDefault')
            ->with(null)
        ;

        // Fire event
        $instance->onPreSubmit($this->event);
    }

    /**
     * Test onPreSubmit with no Definition
     */
    public function testOnPreSubmitWithNoDefinition()
    {
        $instance = $this->getInstance();

        // Stub getData, returns null
        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null)
        ;

        // Stub setDefault, never called
        $this->definition
            ->expects($this->never())
            ->method('setDefault')
            ->with(null)
        ;

        // Fire event
        $instance->onPreSubmit($this->event);
    }

    /**
     * Test onPostSetData function with no states
     */
    public function testOnPostSetDataWithNoStates()
    {
        $instance = $this->getInstance();

        // Stub has, returns false
        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('states')
            ->willReturn(false)
        ;

        // Stub get, must never be called
        $this->form
            ->expects($this->never())
            ->method('get')
            ->with('states')
        ;

        // Fire event
        $instance->onPostSetData($this->event);
    }

    /**
     * Test onPostSetData function with different states
     */
    public function testOnPostSetDataWithDifferentStates()
    {
        $instance = $this->getInstance();

        // Stub has function
        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('states')
            ->willReturn(true)
        ;

        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $states */
        $states = $this->getMock('Symfony\Component\Form\FormInterface');

        /** @var \Integrated\Bundle\WorkflowBundle\Entity\Definition\State | \PHPUnit_Framework_MockObject_MockObject $state */
        $state = $this->getMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        // Get three different form types
        $child1 = $this->getFormChildWithNoData();
        $child2 = $this->getFormChildWithNoDefault($state);
        $child3 = $this->getFormChildWithDefault($state);

        // Stub isDefault, must be called once
        $state
            ->expects($this->once())
            ->method('isDefault')
        ;

        // Stub all, returns array with states
        $states
            ->expects($this->once())
            ->method('all')
            ->willReturn([$child1, $child2, $child3])
        ;

        // Stub get, returns $states
        $this->form
            ->expects($this->once())
            ->method('get')
            ->with('states')
            ->willReturn($states)
        ;

        // Fire event
        $instance->onPostSetData($this->event);
    }

    /**
     * Test onSubmit function
     */
    public function testOnSubmitWithNoDefinition()
    {
        $instance = $this->getInstance();

        // Stub getData, must return null
        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null)
        ;

        // Stub has, must not be called
        $this->form
            ->expects($this->never())
            ->method('has')
            ->with('states')
        ;

        // Fire event
        $instance->onSubmit($this->event);
    }

    public function testOnSubmitWithNoStates()
    {
        $instance = $this->getInstance();

        // Stub getData, must return Definition
        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->definition)
        ;

        // Stub has, must return false
        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('states')
            ->willReturn(false)
        ;

        // Stub get, must not be called
        $this->form
            ->expects($this->never())
            ->method('get')
            ->with('states')
        ;

        // Fire event
        $instance->onSubmit($this->event);
    }


    public function testOnSubmitWithDifferentStates()
    {
        $instance = $this->getInstance();

        // Stub getData, must return Definition
        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->definition)
        ;

        // Stub has, must return false
        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('states')
            ->willReturn(true)
        ;

        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $states */
        $states = $this->getMock('Symfony\Component\Form\FormInterface');

        /** @var \Integrated\Bundle\WorkflowBundle\Entity\Definition\State | \PHPUnit_Framework_MockObject_MockObject $state */
        $state = $this->getMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        // Get three different form types
        $child1 = $this->getFormChildWithNoData();
        $child2 = $this->getFormChildWithNoDefault($state);
        $child3 = $this->getFormChildWithDefaultFalse($state);
        $child4 = $this->getFormChildWithDefaultTrue($state);

        // Stub setDefault, must be called once with $state
        $this->definition
            ->expects($this->once())
            ->method('setDefault')
            ->with($state)
        ;

        // Stub all, must return states
        $states
            ->expects($this->once())
            ->method('all')
            ->willReturn([$child1, $child2, $child3, $child4])
        ;

        // Stub get, must return $states
        $this->form
            ->expects($this->once())
            ->method('get')
            ->with('states')
            ->willReturn($states)
        ;

        // Fire event
        $instance->onSubmit($this->event);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFormChildWithNoData()
    {
        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $child1 */
        $child = $this->getMock('Symfony\Component\Form\FormInterface');

        // Stub getData, returns null
        $child
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null)
        ;

        // Stub has, must never be called
        $child
            ->expects($this->never())
            ->method('has')
            ->with('default')
        ;

        return $child;
    }

    /**
     * @param State $state
     * @return \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFormChildWithNoDefault(State $state)
    {
        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $child2 */
        $child = $this->getMock('Symfony\Component\Form\FormInterface');

        // Stub getData, return $state
        $child
            ->expects($this->once())
            ->method('getData')
            ->willReturn($state)
        ;

        // Stub has, returns false
        $child
            ->expects($this->once())
            ->method('has')
            ->with('default')
            ->willReturn(false)
        ;

        // Stub get, must never be called
        $child
            ->expects($this->never())
            ->method('get')
            ->with('default')
        ;

        return $child;
    }

    /**
     * @param State $state
     * @return \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFormChildWithDefault(State $state)
    {
        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $child3 */
        $child = $this->getMock('Symfony\Component\Form\FormInterface');

        // Stub getData, return $state
        $child
            ->expects($this->once())
            ->method('getData')
            ->willReturn($state)
        ;

        // Stub has, returns true
        $child
            ->expects($this->once())
            ->method('has')
            ->with('default')
            ->willReturn(true)
        ;

        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $default */
        $default = $this->getMock('Symfony\Component\Form\FormInterface');

        // Stub get, returns $default
        $child
            ->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($default)
        ;

        // Stub setData, must be called once
        $default
            ->expects($this->once())
            ->method('setData')
        ;

        return $child;
    }

    /**
     * @param State $state
     * @return \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFormChildWithDefaultFalse(State $state)
    {
        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $child3 */
        $child = $this->getMock('Symfony\Component\Form\FormInterface');

        // Stub getData, returns $state
        $child
            ->expects($this->once())
            ->method('getData')
            ->willReturn($state)
        ;

        // Stub has, returns true
        $child
            ->expects($this->once())
            ->method('has')
            ->with('default')
            ->willReturn(true)
        ;

        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $default */
        $default = $this->getMock('Symfony\Component\Form\FormInterface');

        // Stub get, returns $default
        $child
            ->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($default)
        ;

        // Stub getData, returns false
        $default
            ->expects($this->once())
            ->method('getData')
            ->willReturn(false)
        ;

        return $child;
    }

    /**
     * @param $state
     * @return \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFormChildWithDefaultTrue(State $state)
    {
        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $child4 */
        $child = $this->getMock('Symfony\Component\Form\FormInterface');

        // Stub getData, returns $state
        $child
            ->expects($this->once())
            ->method('getData')
            ->willReturn($state)
        ;

        // Stub has, returns true
        $child
            ->expects($this->once())
            ->method('has')
            ->with('default')
            ->willReturn(true)
        ;

        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $default1 */
        $default = $this->getMock('Symfony\Component\Form\FormInterface');

        // Stub get, returns $default
        $child
            ->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($default)
        ;

        // Stub getData, returns true
        $default
            ->expects($this->once())
            ->method('getData')
            ->willReturn(true)
        ;

        return $child;
    }

    /**
     * @return ExtractDefaultStateFromCollectionListener
     */
    protected function getInstance()
    {
        return new ExtractDefaultStateFromCollectionListener();
    }
}
