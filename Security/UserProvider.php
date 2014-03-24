<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Security;

use Integrated\Bundle\UserBundle\Model\UserManagerInterface;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UserProvider implements UserProviderInterface
{
	/**
	 * @var UserManagerInterface
	 */
	private $manger;

	/**
	 * @param UserManagerInterface $manager
	 */
	public function __construct(UserManagerInterface $manager)
	{
		$this->manger = $manager;

		if (!is_subclass_of($this->manger->getClassName(), 'Integrated\\Bundle\\UserBundle\\Model\\UserInterface')) {
			throw new UnsupportedUserException(sprintf('The user class "%s" is not subclass of Integrated\\Bundle\\UserBundle\\Model\\UserInterface', $this->manger->getClassName()));
		}
	}

	/**
	 * @return UserManagerInterface
	 */
	public function getManager()
	{
		return $this->manger;
	}

	/**
	 * @inheritdoc
	 */
	public function loadUserByUsername($username)
	{
		$user = $this->manger->findByUsername($username);

		if (!$user) {
			$exception = new UsernameNotFoundException(sprintf('No user with the username "%s" exits', $username));
			$exception->setUsername($username);

			throw $exception;
		}

		return $user;
	}

	/**
	 * @inheritdoc
	 */
	public function refreshUser(UserInterface $user)
	{
		if (!$this->supportsClass($user)) {
			throw new UnsupportedUserException(sprintf('The user class "%s" is not a instance or subclass of %s', get_class($user), $this->manger->getClassName()));
		}

		/** @var \Integrated\Bundle\UserBundle\Model\UserInterface $user */

		$loaded = $this->manger->find($user->getId());

		if (!$loaded) {
			$exception = new UsernameNotFoundException(sprintf('The user with id "%s" could not be refreshed', $user->getId()));
			$exception->setUsername($user->getUsername());

			throw $exception;
		}

		return $loaded;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsClass($class)
	{
		if (is_object($class)) {
			$class = get_class($class);
		}

		return $class === $this->manger->getClassName() || is_subclass_of($class, $this->manger->getClassName());
	}
}