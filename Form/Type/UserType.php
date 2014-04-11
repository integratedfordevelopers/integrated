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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Integrated\Bundle\UserBundle\Form\EventListener\UserSubscriber;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class UserType extends AbstractType
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text');
        $builder->add('password', 'password', ['mapped' => false, 'required' => false]);

        $builder->addEventSubscriber(new UserSubscriber('integrated.extension.user', $this->container));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Integrated\\Bundle\\UserBundle\\Model\\User',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_user_profile';
    }
}