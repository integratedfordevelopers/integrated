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

use Integrated\Bundle\UserBundle\Form\SecurityLoginSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class LoginType extends AbstractType
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * Create a login form type used for authentication.
	 *
	 * The container is used to retrieve the request so that the errors
	 * and last username can be extracted from it.
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('_username', 'text');
		$builder->add('_password', 'password');

		if ($options['auth_remember']) {
			$builder->add('_remember_me', 'checkbox', ['required' => false]);
		}

		if ($options['auth_target_path']) {
			$config = [];

			if ($options['auth_target_path'] === (string) $options['auth_target_path']) {
				$config['data'] = $options['auth_target_path'];
				$config['mapped'] = false;
			}

			$builder->add('_target_path', 'hidden', $config);
		}

		$builder->add('login', 'submit');

		if ($request = $this->getRequest()) {
			$builder->addEventSubscriber(new SecurityLoginSubscriber($request));
		}
	}

	/**
	 * @inheritdoc
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['full_name'] = ''; // field names should not be prefixed
	}

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		// form_login csrf token name is by default "_csrf_token"
		// and the intention is by default "authenticate" so set
		// those values as default for this form.

		$resolver->setDefaults([
			'method'           => 'post',
			'csrf_field_name'  => '_csrf_token',
			'intention'        => 'authenticate',

			'auth_remember'    => true,
			'auth_target_path' => null
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'integrated_user_security_login';
	}

	/**
	 * @return Request | null
	 */
	protected function getRequest()
	{
		if ($this->container->has('request')) {
			return $this->container->get('request');
		}

		return null;
	}
}