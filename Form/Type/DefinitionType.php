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

use Integrated\Bundle\UserBundle\Model\GroupManagerInterface;

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
	}

	/**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
		$resolver->setDefaults(array(
			'choices'     => null,
			'choice_list' => function(Options $options) { return $this->getChoiceList(); },
			'multiple'    => false,
			'expanded'    => false
		));
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

	/**
	 * @return GroupManagerInterface
	 */
	public function getManager()
	{
		return $this->manager;
	}
}