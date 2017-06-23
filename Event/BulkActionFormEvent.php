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

use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class BulkActionFormEvent extends Event
{
    /**
     * @var BulkAction
     */
    protected $bulkAction;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @param BulkAction $bulkAction
     * @param FormInterface $form
     */
    public function __construct(BulkAction $bulkAction, FormInterface $form)
    {
        $this->bulkAction = $bulkAction;
        $this->form = $form;
    }

    /**
     * @return BulkAction
     */
    public function getBulkAction()
    {
        return $this->bulkAction;
    }

    /**
     * @param BulkAction $bulkAction
     * @return $this
     */
    public function setBulkAction(BulkAction $bulkAction)
    {
        $this->bulkAction = $bulkAction;
        return $this;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param FormInterface $form
     * @return $this
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
        return $this;
    }
}
