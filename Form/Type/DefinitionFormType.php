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

use Integrated\Bundle\WorkflowBundle\Entity\Definition;

use Integrated\Common\Validator\Constraints\UniqueEntry;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DefinitionFormType extends AbstractType
{
	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text', [
			'constraints' => [
				new NotBlank(),
				new Length(['min' => 3])
			]
		]);

		$builder->add('states', 'bootstrap_collection', [
			'type'         => 'workflow_definition_state',
			'allow_add'    => true,
			'allow_delete' => true,
			'constraints'  => [
				new Count(['min' => 1]),
				new UniqueEntry(['fields' => ['name'], 'caseInsensitive' => true]),
			]
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'empty_data' => function(FormInterface $form) { return new Definition(); },
			'data_class' => 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition',

			'constraints' => new UniqueEntity(['name']),
		));
	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'integrated_workflow_definition';
	}
}