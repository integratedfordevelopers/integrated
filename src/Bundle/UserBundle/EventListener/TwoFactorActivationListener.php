<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\EventListener;

use Integrated\Bundle\UserBundle\Security\TwoFactor\Http\ContextResolverInterface;
use Integrated\Bundle\UserBundle\Security\TwoFactor\Http\WhitelistProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class TwoFactorActivationListener implements EventSubscriberInterface
{
    use TargetPathTrait;

    /**
     * @var ContextResolverInterface
     */
    private $resolver;

    /**
     * @var WhitelistProviderInterface
     */
    private $provider;

    /**
     * @var HttpUtils
     */
    private $utils;

    public function __construct(ContextResolverInterface $resolver, WhitelistProviderInterface $provider, HttpUtils $utils)
    {
        $this->resolver = $resolver;
        $this->provider = $provider;
        $this->utils = $utils;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 6],
        ];
    }

    public function onRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $context = $this->resolver->resolve($event->getRequest());

        if (!$context) {
            return;
        }

        $user = $context->getUser();

        if ($user && !$user->isGoogleAuthenticatorEnabled()) {
            $request = $context->getRequest();

            if ($this->provider->getMatcher($context)->isWhitelisted($request)) {
                return;
            }

            $config = $context->getConfig();

            if ($this->utils->checkRequestPath($request, $config->getFormPath()) || $this->utils->checkRequestPath($request, $config->getCheckPath())) {
                return;
            }

            if (!$request->isXmlHttpRequest()) {
                $this->saveTargetPath($context->getSession(), $context->getFirewall(), $request->getUri());
            }

            $event->setResponse($this->utils->createRedirectResponse($request, $config->getFormPath()));
        }
    }
}
