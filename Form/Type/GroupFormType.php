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
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class GroupFormType extends AbstractType
{
	/**
	 * @var GroupManagerInterface
	 */
	private $manager;

	/**
	 * @param GroupManagerInterface $manager
	 */
	public function __construct(GroupManagerInterface $manager)
	{
		$this->manager = $manager;
	}

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('name', 'text'); // todo: validate unique name
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
			'empty_data' => function(FormInterface $form) { return $this->getManager()->create(); },
            'data_class' => $this->getManager()->getClassName(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_user_group_form';
    }

	/**
	 * @return GroupManagerInterface
	 */
	public function getManager()
	{
		return $this->manager;
	}
}