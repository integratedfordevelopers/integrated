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
use Symfony\Component\Security\Core\Security;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SecurityLoginListener implements EventSubscriberInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * Constructor.
     *
     * @param Request             $request
     * @param TranslatorInterface $translator
     * @param string              $translationDomain
     */
    public function __construct(Request $request, TranslatorInterface $translator, $translationDomain = null)
    {
        $this->request = $request;

        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $request = $this->getRequest();
        $session = $this->getSession();

        $error = null;

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else if ($session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->remove(Security::AUTHENTICATION_ERROR);
        }

        if ($error instanceof AuthenticationException) {
            $event->getForm()->addError(new FormError(
                $this->translator->trans($error->getMessage(), [], $this->translationDomain),
                $error->getMessage(),
                [],
                null,
                $error
            ));
        }

        $event->setData(['_username' => $session && $session->has(Security::LAST_USERNAME) ? $session->get(Security::LAST_USERNAME) : '']);
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
     * @return TranslatorInterface
     */
    protected function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return null | string
     */
    protected function getTranslationDomain()
    {
        return $this->translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData'
        ];
    }
}
