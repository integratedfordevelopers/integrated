<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Tests\Form\Event;

use Integrated\Common\Content\Form\Event\ViewEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ViewEventTest extends FormEventTest
{
    /**
     * @var FormView|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

    /**
     * @var FormInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $form;

    /**
     * @var array
     */
    protected $options = ['value 1', 'value 2', 'key' => 'value'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->view = $this->createMock(FormView::class);
        $this->form = $this->createMock(FormInterface::class);
    }

    public function testGetView()
    {
        self::assertSame($this->view, $this->getInstance()->getView());
    }

    public function testGetForm()
    {
        self::assertSame($this->form, $this->getInstance()->getForm());
    }

    public function testGetOptions()
    {
        self::assertSame($this->options, $this->getInstance()->getOptions());
    }

    /**
     * @return ViewEvent
     */
    protected function getInstance()
    {
        return new ViewEvent($this->type, $this->metadata, $this->view, $this->form, $this->options);
    }
}
