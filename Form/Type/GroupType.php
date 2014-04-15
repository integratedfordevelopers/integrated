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

use Integrated\Bundle\UserBundle\Model\GroupManagerInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class GroupType extends AbstractType
{
	/**
	 * @var GroupManagerInterface
	 */
	private $manager;

	/**
	 * @var ChoiceListInterface
	 */
	private $choicelist;

	/**
	 * @param GroupManagerInterface $manager
	 */
	public function __construct(GroupManagerInterface $manager)
	{
		$this->manager = $manager;
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
        return 'integrated_user_group_type';
    }

	/**
	 * @return ChoiceListInterface
	 */
	public function getChoiceList()
	{
		if ($this->choicelist === null) {
			$this->choicelist = new ObjectChoiceList($this->getManager()->findAll(), 'name', [], null, 'id');
		}

		return $this->choicelist;
	}

	/**
	 * @return GroupManagerInterface
	 */
	public function getManager()
	{
		return $this->manager;
	}
}