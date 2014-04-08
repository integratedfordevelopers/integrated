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

use Integrated\Common\Content\ExtensibleInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class UserSubscriber implements EventSubscriberInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var SecureRandomInterface
	 */
	private $generator;

	/**
	 * @var EncoderFactoryInterface
	 */
	private $encoderFactory;

    /**
     * @var string
     */
	private $name;

	/**
	 * @param string $name
	 * @param ContainerInterface $container
	 */
	public function __construct($name, ContainerInterface $container)
    {
		$this->container = $container;
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT => 'postSubmit'
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        if (!$parent = $event->getForm()->getParent()) {
			return;
		}

        $content = $parent->getNormData();

		if ($content instanceof ExtensibleInterface) {
			$event->setData($content->getExtension($this->getName()));
		}
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
		if (!$parent = $event->getForm()->getParent()) {
			return;
		}

		$content = $parent->getNormData();

		if ($content instanceof ExtensibleInterface) {
			$user = $event->getForm()->getData();

			// if a password is entered it need to be encoded and stored in
			// the user model.

			if ($password = $event->getForm()->get('password')->getData()) {
				$salt = base64_encode($this->getGenerator()->nextBytes(72));

				$user->setPassword($this->getEncoder($user)->encodePassword($password, $salt));
				$user->setSalt($salt);
			}

			$content->setExtension($this->getName(), $user); // should not be required
		}
    }

	/**
	 * @return string
	 */
	protected function getName()
	{
		return $this->name;
	}

	/**
	 * @return SecureRandomInterface
	 */
	protected function getGenerator()
	{
		if ($this->generator === null) {
			$this->generator = $this->getContainer()->get('security.secure_random');
		}

		return $this->generator;
	}

	/**
	 * @param object $user
	 * @return PasswordEncoderInterface
	 */
	protected function getEncoder($user)
	{
		if ($this->encoderFactory === null) {
			$this->encoderFactory = $this->getContainer()->get('security.encoder_factory');
		}

		return $this->encoderFactory->getEncoder($user);
	}

	/**
	 * @return ContainerInterface
	 */
	protected function getContainer()
	{
		return $this->container;
	}
}