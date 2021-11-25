<?php
/**
 * This file is part of BraincraftedBootstrapBundle.
 * (c) 2012-2013 by Florian Eckerstorfer.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * TypeSetterExtension.
 *
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright  2012-2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 *
 * @see       http://bootstrap.braincrafted.com Bootstrap for Symfony2
 */
class TypeSetterExtension extends AbstractTypeExtension
{
    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['original_type'] = $form->getConfig()->getType()->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
