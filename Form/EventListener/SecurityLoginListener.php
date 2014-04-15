<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SecurityLoginListener implements EventSubscriberInterface
{
	/**
	 * @var Request
	 */
	private $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function preSetData(FormEvent $event)
	{
		$request = $this->getRequest();
		$session = $this->getSession();

		$error = null;

		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} else if ($session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}

		if ($error instanceof AuthenticationException) {
			$event->getForm()->addError(new FormError($error->getMessage()));
		}

		$event->setData(['_username' => $session && $session->has(SecurityContext::LAST_USERNAME) ? $session->get(SecurityContext::LAST_USERNAME) : '']);
	}

	/**
	 * @return Request
	 */
	protected function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return null | SessionInterface
	 */
	protected function getSession()
	{
		return $this->request->getSession();
	}

	/**
	 * @inheritdoc
	 */
	public static function getSubscribedEvents()
	{
		return [
			FormEvents::PRE_SET_DATA => 'preSetData'
		];
	}
}