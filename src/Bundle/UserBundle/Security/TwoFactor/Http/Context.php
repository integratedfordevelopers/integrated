<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Security\TwoFactor\Http;

use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\UserBundle\Security\TwoFactor\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Context implements ContextInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @var string
     */
    private $firewall;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Request $request, TokenInterface $token, string $firewall, Config $config)
    {
        $this->request = $request;
        $this->token = $token;
        $this->firewall = $firewall;
        $this->config = $config;
    }

    public function getToken(): TokenInterface
    {
        return $this->token;
    }

    public function getUser(): ?UserInterface
    {
        $user = $this->token->getUser();

        if ($user instanceof UserInterface) {
            return $user;
        }

        return null;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getSession(): SessionInterface
    {
        return $this->request->getSession();
    }

    public function getFirewall(): string
    {
        return $this->firewall;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
