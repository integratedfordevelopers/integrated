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

interface ContextInterface
{
    public function getToken(): TokenInterface;

    public function getUser(): ?UserInterface;

    public function getRequest(): Request;

    public function getSession(): SessionInterface;

    public function getFirewall(): string;

    public function getConfig(): Config;
}
