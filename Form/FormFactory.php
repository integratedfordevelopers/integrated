<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\Form;

use Symfony\Component\Form\FormFactory as FormFactorySymfony;

use Integrated\Common\Content\Form\FormFactory as FormFactoryIntegrated;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class FormFactory
{
    /**
     * @var FormFactorySymfony
     */
    protected $formFactorySymfony;

    /**
     * @var FormFactoryIntegrated
     */
    protected $formFactoryIntegrated;

    /**
     * @param FormFactorySymfony $formFactorySymfony
     * @param FormFactoryIntegrated $formFactoryIntegrated
     */
    public function __construct(FormFactorySymfony $formFactorySymfony, FormFactoryIntegrated $formFactoryIntegrated)
    {
        $this->formFactorySymfony = $formFactorySymfony;
        $this->formFactoryIntegrated = $formFactoryIntegrated;
    }

    /**
     * @param string $contentType
     * @param ContentInterface $data
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function create($contentType, ContentInterface $data = null)
    {
        return $this->formFactorySymfony->create($this->formFactoryIntegrated->getType($contentType), $data);
    }
}
