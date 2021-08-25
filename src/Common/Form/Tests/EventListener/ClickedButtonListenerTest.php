<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Tests\EventListener;

use ArrayIterator;
use Integrated\Common\Form\EventListener\ClickedButtonListener;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ClickedButtonListenerTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Symfony\\Component\\EventDispatcher\\EventSubscriberInterface', $this->getInstance());
    }

    public function testGetSubscribedEvents()
    {
        self::assertEquals([FormEvents::SUBMIT => 'onSubmit'], $this->getInstance()->getSubscribedEvents());
    }

    public function testOnSubmit()
    {
        $event = $this->getEvent($this->getForm([
            $this->getForm(),
            $this->getButton(false),
            $this->getButton(false),
            $this->getButton(true, 'button 3'),
            $this->getButton(false),
            $this->getForm(),
        ]));

        $event->expects($this->once())
            ->method('setData')
            ->with($this->equalTo('button 3'));

        $this->getInstance()->onSubmit($event);
    }

    public function testOnSubmitEmpty()
    {
        $event = $this->getEvent($this->getForm([]));
        $event->expects($this->never())
                ->method('setData');

        $this->getInstance()->onSubmit($event);
    }

    /**
     * @return ClickedButtonListener
     */
    protected function getInstance()
    {
        return new ClickedButtonListener();
    }

    /**
     * @return FormEvent|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEvent(FormInterface $form)
    {
        $mock = $this->getMockBuilder('Symfony\\Component\\Form\\FormEvent')->disableOriginalConstructor()->getMock();
        $mock->expects($this->once())
            ->method('getForm')
            ->willReturn($form);

        return $mock;
    }

    /**
     * @return FormInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getForm(array $children = null)
    {
        // Form implements FormInterface and has a valid iterator interface
        $mock = $this->getMockBuilder('Symfony\\Component\\Form\\Form')->disableOriginalConstructor()->getMock();

        if (null !== $children) {
            $mock->expects($this->once())
                ->method('getIterator')
                ->willReturn(new ArrayIterator($children));
        }

        return $mock;
    }

    /**
     * @param bool $clicked
     *
     * @return ClickableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getButton($clicked, $name = null)
    {
        // SubmitButton implements FormInterface and ClickableInterface
        $mock = $this->getMockBuilder('Symfony\\Component\\Form\\SubmitButton')->disableOriginalConstructor()->getMock();
        $mock->expects($this->once())
            ->method('isClicked')
            ->willReturn($clicked);

        if (null !== $name) {
            $mock->expects($this->once())
                ->method('getName')
                ->willReturn($name);
        }

        return $mock;
    }
}
