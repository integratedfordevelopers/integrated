<?php

namespace Integrated\Bundle\CommentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CommentType
 */
class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text', 'text', ['attr' => ['placeholder' => 'Add a comment']]);
        $builder->add('parent', 'hidden', ['data' => $options['parent']]);
        $builder->add('field', 'hidden', ['data' => $options['field']]);
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_comment';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('parent', null);
        $resolver->setDefault('field', null);
    }
}
