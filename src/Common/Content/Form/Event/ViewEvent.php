<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form\Event;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ViewEvent extends FormEvent
{
    /**
     * @var FormView
     */
    private $view;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var array
     */
    private $options = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(
        ContentTypeInterface $contentType,
        MetadataInterface $metadata,
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        parent::__construct($contentType, $metadata);

        $this->view = $view;
        $this->form = $form;
        $this->options = $options;
    }

    /**
     * @return FormView
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
