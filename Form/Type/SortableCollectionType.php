<?php

namespace Integrated\Bundle\FormTypeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SortableCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace(
            $view->vars,
            [
                'default_title' => $options['default_title']
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'default_title' => 'Item'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'bootstrap_collection';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'integrated_sortable_collection';
    }
}
