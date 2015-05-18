<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Bundle\WorkflowBundle\Form\DataTransformer\PermissionTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PermissionsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new PermissionTransformer());

        $builder->add('read', 'user_group_choice', ['required' => false, 'multiple' => true, 'choice_data' => 'scalar']);
        $builder->add('write', 'user_group_choice', ['required' => false, 'multiple' => true, 'choice_data' => 'scalar']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('empty_data', function(FormInterface $form) { return new ArrayCollection(); });
        $resolver->setDefault('label', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_workflow_definition_permissions';
    }
}
