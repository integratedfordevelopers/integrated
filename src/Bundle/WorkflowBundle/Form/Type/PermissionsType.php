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
use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Bundle\UserBundle\Form\Type\GroupType;
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
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new PermissionTransformer($this->repository));

        $builder->add('read', GroupType::class, ['required' => false, 'multiple' => true, 'attr' => ['class' => 'select2']]);
        $builder->add('write', GroupType::class, ['required' => false, 'multiple' => true, 'choice_data' => 'scalar', 'attr' => ['class' => 'select2']]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = function (FormInterface $form) {
            return new ArrayCollection();
        };

        $resolver->setDefault('empty_data', $emptyData);
        $resolver->setDefault('label', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_workflow_definition_permissions';
    }
}
