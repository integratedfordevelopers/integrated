<?php

namespace Integrated\Bundle\CommentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text', 'text', ['attr' => ['placeholder' => 'Add a comment']]);
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_comment';
    }

}

