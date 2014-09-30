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

        // Stub setDefault
        $this->definition
            ->expects($this->once())
            ->method('setDefault')
            ->with(null)
            ->willReturn(null)
        ;

        // Fire event
        $instance->onPreSubmit($this->event);
    }

    /**
     * Test onPreSubmit with no definition
     */
    public function testOnPreSubmitWithNoDefinition()
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
            ->willReturn(null)
        ;

        // Stub setDefault
        $this->definition
            ->expects($this->never())
            ->method('setDefault')
            ->with(null)
            ->willReturn(null)
        ;

        // Fire event
        $instance->onPreSubmit($this->event);
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
     * Test onSubmit function
     */
    public function testOnSubmit()
    {
        // TODO test onSubmit function
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
