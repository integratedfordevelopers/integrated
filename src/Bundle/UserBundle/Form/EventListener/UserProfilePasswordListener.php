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

use Integrated\Bundle\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UserProfilePasswordListener implements EventSubscriberInterface
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @param EncoderFactoryInterface $encoder
     */
    public function __construct(EncoderFactoryInterface $encoder)
    {
        $this->encoderFactory = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'onPostSetData',
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSetData(FormEvent $event)
    {
        $inheritedPasswordOptions = $event->getForm()->get('password')->getConfig()->getOptions();

        if ($event->getData() === null || !$event->getData()->getPassword()) {
            // password is not set, so it should not be blank
            $inheritedPasswordOptions['constraints'][] = new NotBlank();
        } else {
            // make password optional, it is already set
            $inheritedPasswordOptions['attr']['help_text'] = 'Password will only be changed if a new password is entered';
            $inheritedPasswordOptions['required'] = false;
        }

        $event->getForm()->add('password', PasswordType::class, $inheritedPasswordOptions);
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $user = $form->getData();

        if (!$user instanceof UserInterface) {
            return; // not a user so nothing to encode
        }

        // if a password is entered it need to be encoded and stored in
        // the user model.

        if ($password = $form->get('password')->getData()) {
            $salt = base64_encode(random_bytes(72));

            $user->setPassword($this->getEncoder($user)->encodePassword($password, $salt));
            $user->setSalt($salt);
        }
    }

    /**
     * @param object $user
     *
     * @return PasswordEncoderInterface
     */
    protected function getEncoder($user)
    {
        return $this->encoderFactory->getEncoder($user);
    }
}
