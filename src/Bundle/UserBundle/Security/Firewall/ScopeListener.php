<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Security\Firewall;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Firewall\AbstractListener;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\UserBundle\Model\Scope;

class ScopeListener extends AbstractListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var string
     */
    private $providerKey;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param string                $providerKey
     */
    public function __construct(TokenStorageInterface $tokenStorage, $providerKey)
    {
        $this->tokenStorage = $tokenStorage;
        $this->providerKey = $providerKey;
    }

    public function supports(Request $request): ?bool
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return false;
        }

        $user = $token->getUser();

        return !(!$token instanceof TokenInterface || $token instanceof TwoFactorTokenInterface) && $user instanceof UserInterface;
    }

    public function authenticate(RequestEvent $event)
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();
        $scope = $user->getScope();

        if (!$scope instanceof Scope || !$scope->isAdmin()) {
            return;
        }

        $roles = $user->getRoles();

        $roles[] = 'ROLE_SCOPE_INTEGRATED';

        $newToken = new UsernamePasswordToken(
            $user,
            $token->getCredentials(),
            $this->providerKey,
            $roles
        );

        $this->tokenStorage->setToken($newToken);
    }
}
