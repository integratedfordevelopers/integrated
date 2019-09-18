<?php

namespace Integrated\Bundle\UserBundle\Form\Type;

use Integrated\Bundle\UserBundle\Model\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Integrated\Bundle\UserBundle\Model\Scope;

class UserFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');

        $builder
            ->add('q', TextType::class, ['attr' => ['placeholder' => 'Filter username']])
            ->add('groups', GroupType::class, ['class' => Group::class, 'multiple' => true, 'expanded' => true,])
            ->add('scope', GroupType::class, ['class' => Scope::class, 'multiple' => true, 'expanded' => true,]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_user_filter';
    }
}
