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

use Integrated\Bundle\UserBundle\Security\TwoFactor\Http\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AuthenticatorErrorListener implements EventSubscriberInterface
{
    public const INVALID_CODE_ERROR = '_integrated_user.authenticator.last_error';

    /**
     * @var Context
     */
    private $context;

    /**
     * @var string
     */
    private $name;

    public function __construct(Context $context, string $name)
    {
        $this->context = $context;
        $this->name = $name;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $session = $this->context->getSession();

        if ($session->has(self::INVALID_CODE_ERROR)) {
            $form = $event->getForm()->get($this->name);

            foreach (unserialize($session->remove(self::INVALID_CODE_ERROR)) as $error) {
                if ($error instanceof FormError) {
                    $form->addError($error);
                }
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $session = $this->context->getSession();
        $session->remove(self::INVALID_CODE_ERROR);

        if ($errors = iterator_to_array($event->getForm()->get($this->name)->getErrors())) {
            $session->set(self::INVALID_CODE_ERROR, serialize($errors));
        }
    }
}
