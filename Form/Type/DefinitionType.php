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

use Integrated\Bundle\WorkflowBundle\Form\DataTransformer\DefinitionTransformer;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\Common\Persistence\ObjectRepository;

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
	 * @var ChoiceListInterface
	 */
	private $choiceList;

	/**
	 * @param ObjectRepository $repository
	 */
	public function __construct(ObjectRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		if ($options['multiple']) {
			$builder->addViewTransformer(new CollectionToArrayTransformer(), true);
		}

		if ($options['data_type'] != 'object') {
			$builder->addModelTransformer(new DefinitionTransformer());
		}
	}

	/**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
		$resolver->setDefaults([
			'empty_value' => 'None',
			'empty_data'  => null,
			'required'    => false,

			'choices'     => null,
			'choice_list' => function(Options $options) { return $this->getChoiceList(); },
			'multiple'    => false,
			'expanded'    => false,

			'data_type'   => 'object'
		]);

		$resolver->addAllowedValues(['data_type' => ['scalar', 'object']]);
    }

	/**
	 * @inheritdoc
	 */
	public function getParent()
	{
		return 'choice';
	}

	/**
     * @inheritdoc
     */
    public function getName()
    {
        return 'integrated_workflow_definition_type';
    }

	/**
	 * @return ChoiceListInterface
	 */
	public function getChoiceList()
	{
		if ($this->choiceList === null) {
			$this->choiceList = new ObjectChoiceList($this->repository->findAll(), 'name', [], null, 'id');
		}

		return $this->choiceList;
	}
}