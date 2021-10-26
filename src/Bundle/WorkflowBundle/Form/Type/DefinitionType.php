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

use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\WorkflowBundle\Form\DataTransformer\DefinitionTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DefinitionType extends AbstractType
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
        $builder->addModelTransformer(new DefinitionTransformer($this->repository));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $classNormalizer = function (Options $options) {
            return $this->repository->getClassName(); // force the class to always be the same as the repository
        };

        $resolver->setNormalizer('class', $classNormalizer);
        $resolver->setDefault('class', $this->repository->getClassName());
        $resolver->setDefault('choice_value', 'id');
        $resolver->setDefault('choice_label', 'name');
        $resolver->setDefault('placeholder', 'None');
        $resolver->setDefault('empty_data', null);
        $resolver->setDefault('required', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_workflow_definition_choice';
    }
}
