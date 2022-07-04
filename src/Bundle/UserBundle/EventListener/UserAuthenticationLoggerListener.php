<?php

namespace Integrated\Bundle\UserBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class UserAuthenticationLoggerListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RequestStack
     */
    private $requets;

    public function __construct(LoggerInterface $logger, RequestStack $requets)
    {
        $this->logger = $logger;
        $this->requets = $requets;
    }

    public static function getSubscribedEvents()
    {
        return [
            LoginSuccessEvent::class => 'onLogin',
        ];
    }

    public function onLogin(LoginSuccessEvent $event): void
    {
        $context = [
            'firewall' => $event->getFirewallName(),
            'user' => $event->getUser()->getUserIdentifier(),
        ];

        if ($request = $this->requets->getCurrentRequest()) {
            // start the session in case it isn't stated yet
            $session = $request->getSession();
            $session->start();

            $context['session'] = $session->getId();
            $context['user-agent'] = $request->headers->get('User-Agent');
        }

        $this->logger->info('Login event', $context);
    }
}
