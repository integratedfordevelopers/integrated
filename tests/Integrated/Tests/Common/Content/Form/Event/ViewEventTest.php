<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Content\Form\Event;

use Integrated\Common\Content\Form\Event\ViewEvent;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ViewEventTest extends FormEventTest
{
    /**
     * @var FormInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

    /**
     * @var FormView | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $form;

    protected function setUp()
    {
        parent::setUp();

        $this->view = $this->getMock('Symfony\\Component\\Form\\FormView');
        $this->form = $this->getMock('Symfony\\Component\\Form\\FormInterface');
    }

    public function testGetView()
    {
        self::assertSame($this->view, $this->getInstance()->getView());
    }

    public function testGetForm()
    {
        self::assertSame($this->form, $this->getInstance()->getForm());
    }

    public function testSetAndGetOptions()
    {
        $event = $this->getInstance();

        self::assertSame([], $event->getOptions());

        $options = ['value 1', 'value 2', 'key' => 'value'];
        $event->setOptions($options);

        self::assertSame($options, $event->getOptions());
    }

    /**
     * @return ViewEvent
     */
    protected function getInstance()
    {
        return new ViewEvent($this->type, $this->metadata, $this->view, $this->form);
    }
}
