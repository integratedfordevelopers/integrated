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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Integrated\Bundle\UserBundle\Form\EventListener\UserProfileListener;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ProfileFormType extends AbstractType
{
	/**
	 * @var UserManagerInterface
	 */
	private $manager;

	/**
	 * @var SecureRandomInterface
	 */
	private $generator;

	/**
	 * @var EncoderFactoryInterface
	 */
	private $encoderFactory;

	/**
	 * @param UserManagerInterface $manager
	 * @param SecureRandomInterface $generator
	 * @param EncoderFactoryInterface $encoder
	 */
	public function __construct(UserManagerInterface $manager, SecureRandomInterface $generator, EncoderFactoryInterface $encoder)
	{
		$this->manager = $manager;

		$this->generator = $generator;
		$this->encoderFactory = $encoder;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username', 'text');

		$builder->add('password', 'password', [
			'mapped' => false,
			'required' => false,
			'attr' => ['help_text' => 'Password will only be changed if a new password is entered']
		]);

		$builder->add('groups', 'user_group_choice', ['required' => false]);

		$builder->addEventSubscriber(new UserProfileListener('integrated.extension.user', $this->generator, $this->encoderFactory));
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
		return 'integrated_user_profile_form';
	}

	/**
	 * @return UserManagerInterface
	 */
	public function getManager()
	{
		return $this->manager;
	}
}