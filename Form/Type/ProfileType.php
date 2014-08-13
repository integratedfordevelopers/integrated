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

use Integrated\Bundle\UserBundle\Model\UserManagerInterface;

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
class ProfileType extends AbstractType
{
	/**
	 * @var UserManagerInterface
	 */
	private $manager;

	/**
	 * @var ChoiceListInterface
	 */
	private $choiceList;

	/**
	 * @param UserManagerInterface $manager
	 */
	public function __construct(UserManagerInterface $manager)
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
        return 'integrated_user_profile_type';
    }

	/**
	 * @return ChoiceListInterface
	 */
	public function getChoiceList()
	{
		if ($this->choiceList === null) {
			$this->choiceList = new ObjectChoiceList($this->getManager()->findAll(), 'username', [], null, 'id');
		}

		return $this->choiceList;
	}

	/**
	 * @return UserManagerInterface
	 */
	public function getManager()
	{
		return $this->manager;
	}
}