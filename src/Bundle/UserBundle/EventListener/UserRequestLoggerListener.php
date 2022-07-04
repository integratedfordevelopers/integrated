<?php

namespace Integrated\Bundle\UserBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\FirewallMapInterface;

class UserRequestLoggerListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @var FirewallMapInterface
     */
    private $map;

    public function __construct(LoggerInterface $logger, TokenStorageInterface $storage, FirewallMapInterface $map)
    {
        $this->logger = $logger;
        $this->storage = $storage;
        $this->map = $map;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 7],
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->getUser();

        if (!$user) {
            return;
        }

        $request = $event->getRequest();

        $context = [
            'firewall' => $this->getFirewall($request),
            'user' => $user->getUserIdentifier(),
            'session' => $request->getSession()->getId(),
            'methd' => $request->getMethod(),
            'uri' => $request->getUri(),
            'token' => \get_class($this->storage->getToken()),
        ];

        $this->logger->info('Request event', $context);
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $this->getUser();

        if (!$user) {
            return;
        }

        $request = $event->getRequest();

        $context = [
            'firewall' => $this->getFirewall($request),
            'user' => $user->getUserIdentifier(),
        ];

        $this->logger->info('Logout event', $context);
    }

    private function getUser(): ?UserInterface
    {
        $token = $this->storage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        if (!$user) {
            return null;
        }

        return $user;
    }

    private function getFirewall(Request $request): ?string
    {
        if ($this->map instanceof FirewallMap && $config = $this->map->getFirewallConfig($request)) {
            return $config->getName();
        }

        return null;
    }
}
