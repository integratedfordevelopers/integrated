<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class HandleRequestEvent extends Event
{
    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @param FormInterface $form
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->form->getData();
    }
}
