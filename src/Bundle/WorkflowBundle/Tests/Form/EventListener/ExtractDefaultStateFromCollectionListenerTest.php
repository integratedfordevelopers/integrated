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
class ExtractDefaultStateFromCollectionListenerTest extends \PHPUnit\Framework\TestCase
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
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->event = $this->getMockBuilder('Symfony\Component\Form\FormEvent')->disableOriginalConstructor()->getMock();
        $this->form = $this->createMock('Symfony\Component\Form\FormInterface');
        $this->definition = $this->createMock('Integrated\Bundle\WorkflowBundle\Entity\Definition');

        $this->event
            ->expects($this->any())
            ->method('getForm')
            ->willReturn($this->form)
        ;
    }

    /**
     * Test instance of.
     */
    public function testInstanceOf()
    {
        $instance = $this->getInstance();
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $instance);
    }

    /**
     * Test getSubscribedEvents function.
     */
    public function testGetSubscribedEvents()
    {
        $instance = $this->getInstance();

        $events = [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::POST_SET_DATA => 'onPostSetData',
            FormEvents::SUBMIT => 'onSubmit',
        ];

        $this->assertSame($events, $instance::getSubscribedEvents());
    }

    /**
     * Test onPreSubmit event.
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
     * Test onPreSubmit with inValid Definition.
     */
    public function testOnPreSubmitWithInvalidDefinition()
    {
        $instance = $this->getInstance();

        $invalidDefinition = $this->createMock('stdClass');
        $invalidDefinition
            ->expects($this->never())
            ->method($this->anything())
        ;

        // Stub getData, returns null
        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($invalidDefinition)
        ;

        // Fire event
        $instance->onPreSubmit($this->event);
    }

    /**
     * Test onPostSetData function with no states.
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
        ;

        // Fire event
        $instance->onPostSetData($this->event);
    }

    /**
     * Test onPostSetData function with invalid state.
     */
    public function testOnPostSetDataWithInvalidState()
    {
        $child = $this->getChild($this->createMock('stdClass'));
        $child
            ->expects($this->never())
            ->method('has')
        ;

        $states = $this->getForm();
        $states
            ->expects($this->once())
            ->method('all')
            ->willReturn([$child])
        ;

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('states')
            ->willReturn(true)
        ;

        $this->form
            ->expects($this->once())
            ->method('get')
            ->with('states')
            ->willReturn($states)
        ;

        $this->getInstance()->onPostSetData($this->event);
    }

    /**
     * Test onPostSetData function with no default state.
     */
    public function testOnPostSetDataWithNoDefaultState()
    {
        $state = $this->getState(false);

        $child = $this->getChild($state, false);
        $child
            ->expects($this->once())
            ->method('getData')
            ->willReturn($state)
        ;

        $states = $this->getForm();
        $states
            ->expects($this->once())
            ->method('all')
            ->willReturn([$child])
        ;

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('states')
            ->willReturn(true)
        ;

        $this->form
            ->expects($this->once())
            ->method('get')
            ->with('states')
            ->willReturn($states)
        ;

        $this->getInstance()->onPostSetData($this->event);
    }

    /**
     * Test onPostSetData with default state.
     */
    public function testOnPostSetDataWithDefaultState()
    {
        $state = $this->getState(true);

        $child = $this->getChild($state, true);
        $child
            ->expects($this->once())
            ->method('getData')
            ->willReturn($state)
        ;

        $states = $this->getForm();
        $states
            ->expects($this->once())
            ->method('all')
            ->willReturn([$child])
        ;

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('states')
            ->willReturn(true)
        ;

        $this->form
            ->expects($this->once())
            ->method('get')
            ->with('states')
            ->willReturn($states)
        ;

        $this->getInstance()->onPostSetData($this->event);
    }

    /**
     * Test onSubmit function.
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
        ;

        // Fire event
        $instance->onSubmit($this->event);
    }

    /**
     * Test onSubmit function with no states.
     */
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
        ;

        // Fire event
        $instance->onSubmit($this->event);
    }

    /**
     * Test onSubmit function with states.
     */
    public function testOnSubmitWithStates()
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

        $states = $this->getForm();

        /** @var \Integrated\Bundle\WorkflowBundle\Entity\Definition\State | \PHPUnit_Framework_MockObject_MockObject $state1 */
        $state1 = $this->createMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        /** @var \Integrated\Bundle\WorkflowBundle\Entity\Definition\State | \PHPUnit_Framework_MockObject_MockObject $state2 */
        $state2 = $this->createMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        /** @var \Integrated\Bundle\WorkflowBundle\Entity\Definition\State | \PHPUnit_Framework_MockObject_MockObject $state3 */
        $state3 = $this->createMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        // Get three different form types
        $child1 = $this->getFormChild();
        $child2 = $this->getFormChild($state1);
        $child3 = $this->getFormChild($state2, false);
        $child4 = $this->getFormChild($state3, true);

        // Stub setDefault, must be called once with $state
        $this->definition
            ->expects($this->once())
            ->method('setDefault')
            ->with($this->identicalTo($state3))
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
     * @param State  $state
     * @param null   $withDefaultState
     * @param string $getOrSet
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFormChild(State $state = null, $withDefaultState = null, $getOrSet = 'get')
    {
        /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $child1 */
        $child = $this->createMock('Symfony\Component\Form\FormInterface');

        // Stub getData, returns $state
        $child
            ->expects($this->once())
            ->method('getData')
            ->willReturn($state)
        ;

        if (null === $state) {
            // Stub has, must never be called when state is null
            $child
                ->expects($this->never())
                ->method('has')
            ;
        } else {
            if (null === $withDefaultState) {
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
                ;
            } else {
                // Stub has, returns true
                $child
                    ->expects($this->once())
                    ->method('has')
                    ->with('default')
                    ->willReturn(true)
                ;

                /** @var \Symfony\Component\Form\FormInterface | \PHPUnit_Framework_MockObject_MockObject $default */
                $default = $this->createMock('Symfony\Component\Form\FormInterface');

                // Stub get, returns $default
                $child
                    ->expects($this->once())
                    ->method('get')
                    ->with('default')
                    ->willReturn($default)
                ;

                // Stub get or setData
                if ($getOrSet == 'get') {
                    $default
                        ->expects($this->once())
                        ->method('getData')
                        ->willReturn($withDefaultState)
                    ;
                } else {
                    $default
                        ->expects($this->once())
                        ->method('setData')
                        ->with(true)
                    ;
                }
            }
        }

        return $child;
    }

    /**
     * @return ExtractDefaultStateFromCollectionListener
     */
    protected function getInstance()
    {
        return new ExtractDefaultStateFromCollectionListener();
    }

    /**
     * @param mixed $default
     *
     * @return \PHPUnit_Framework_MockObject_MockObject | \Integrated\Bundle\WorkflowBundle\Entity\Definition\State
     */
    protected function getState($default = null)
    {
        $mock = $this->createMock('Integrated\Bundle\WorkflowBundle\Entity\Definition\State');

        if (null !== $default) {
            $mock
                ->expects($this->once())
                ->method('isDefault')
                ->willReturn($default)
            ;
        }

        return $mock;
    }

    /**
     * @param mixed $data
     *
     * @return \PHPUnit_Framework_MockObject_MockObject | \Symfony\Component\Form\FormInterface'
     */
    protected function getForm($data = null)
    {
        $mock = $this->createMock('Symfony\Component\Form\FormInterface');
        $mock
            ->expects($this->any())
            ->method('getData')
            ->willReturn($data)
        ;

        return $mock;
    }

    /**
     * @param mixed $state
     * @param mixed $default
     *
     * @return \PHPUnit_Framework_MockObject_MockObject | \Integrated\Bundle\WorkflowBundle\Entity\Definition\State
     */
    protected function getChild($state = null, $default = null)
    {
        $mock = $this->getForm($state);

        if (null !== $default) {
            $child = $this->getForm();
            $child
                ->expects($this->once())
                ->method('setData')
                ->with($this->equalTo($default))
                ->willReturnSelf()
            ;

            $mock
                ->expects($this->atLeastOnce())
                ->method('has')
                ->with($this->equalTo('default'))
                ->willReturn(true)
            ;

            $mock
                ->expects($this->atLeastOnce())
                ->method('get')
                ->with($this->equalTo('default'))
                ->willReturn($child)
            ;
        }

        return $mock;
    }
}
