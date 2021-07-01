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
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;

class AuthenticatorCheckerListener implements EventSubscriberInterface
{
    /**
     * @var GoogleAuthenticatorInterface
     */
    private $authenticator;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $translationDomain;

    public function __construct(UserInterface $user, GoogleAuthenticatorInterface $authenticator, TranslatorInterface $translator, string $translationDomain = null)
    {
        $this->user = $user;
        $this->authenticator = $authenticator;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onSubmit',
        ];
    }

    public function onSubmit(FormEvent $event)
    {
        if (!$this->authenticator->checkCode($this->user, $event->getData())) {
            $event->getForm()->addError(new FormError(
                $this->translator->trans('code_invalid', [], $this->translationDomain),
                'code_invalid',
                [],
                null
            ));
        }
    }
}
