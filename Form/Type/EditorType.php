<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Johnny Borg <johnnny@e-active.nl>
 */
class EditorType extends AbstractType
{
    const RELATION = '__editor_image';

    /**
     * @var array
     */
    private $contentStyles;

    /**
     * @param array $contentStyles
     */
    public function __construct(array $contentStyles)
    {
        $this->contentStyles = $contentStyles;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['mode'] = $options['mode'];
        $view->vars['content_styles'] = $this->contentStyles;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'mode' => 'default',
        ]);

        $resolver->setAllowedTypes([
            'mode' => 'string',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_editor';
    }
}
