<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Form\Type;

use Integrated\Bundle\UserBundle\Model\RoleManagerInterface;

use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RoleType extends AbstractType
{
	/**
	 * @var RoleManagerInterface
	 */
	private $manager;

	/**
	 * @var ChoiceListInterface
	 */
	private $choiceList;

	/**
	 * @param RoleManagerInterface $manager
	 */
	public function __construct(RoleManagerInterface $manager)
	{
		$this->manager = $manager;
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
			'multiple'    => true,
			'expanded'    => true
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
        return 'integrated_user_role_type';
    }

	/**
	 * @return ChoiceListInterface
	 */
	public function getChoiceList()
	{
		if ($this->choiceList === null) {
			$list = [];

			foreach ($this->getManager()->findAll() as $item) {
				if (!$item->isHidden()) {
					$list[] = $item;
				}
			}

			$this->choiceList = new ObjectChoiceList($list, 'role', [], null, 'id');
		}

		return $this->choiceList;
	}

	/**
	 * @return RoleManagerInterface
	 */
	public function getManager()
	{
		return $this->manager;
	}
}