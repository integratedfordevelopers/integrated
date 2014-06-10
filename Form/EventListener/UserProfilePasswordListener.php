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

use Integrated\Common\Content\Extension\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;

use Symfony\Component\Validator\Constraints\Length;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UserProfilePasswordListener implements EventSubscriberInterface
{
	/**
	 * @var SecureRandomInterface
	 */
	private $generator;

	/**
	 * @var EncoderFactoryInterface
	 */
	private $encoderFactory;

	/**
	 * @param SecureRandomInterface $generator
	 * @param EncoderFactoryInterface $encoder
	 */
	public function __construct(SecureRandomInterface $generator, EncoderFactoryInterface $encoder)
    {
		$this->generator = $generator;
		$this->encoderFactory = $encoder;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
			FormEvents::POST_SET_DATA => 'onPostSetData',
            FormEvents::POST_SUBMIT => 'onPostSubmit'
        );
    }

	public function onPostSetData(FormEvent $event)
	{
		if ($event->getData() === null || !$event->getData()->getPassword()) {
			return;
		}

		// replace required password field with optional password field

		$event->getForm()->add('password', 'password', [
			'mapped' => false,
			'required' => false,
			'attr' => ['help_text' => 'Password will only be changed if a new password is entered'],
			'constraints' => [
				new Length(['min' => 6])
			]
		]);
	}

    /**
     * @param FormEvent $event
     */
    public function onPostSubmit(FormEvent $event)
    {
		$user = $event->getForm()->getData();

		// if a password is entered it need to be encoded and stored in
		// the user model.

		if ($password = $event->getForm()->get('password')->getData()) {
			$salt = base64_encode($this->getGenerator()->nextBytes(72));

			$user->setPassword($this->getEncoder($user)->encodePassword($password, $salt));
			$user->setSalt($salt);
		}
    }

	/**
	 * @return SecureRandomInterface
	 */
	protected function getGenerator()
	{
		return $this->generator;
	}

	/**
	 * @param object $user
	 * @return PasswordEncoderInterface
	 */
	protected function getEncoder($user)
	{
		return $this->encoderFactory->getEncoder($user);
	}
}