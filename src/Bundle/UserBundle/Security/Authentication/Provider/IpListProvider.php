<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class IpListProvider implements AuthenticationProviderInterface
{
    public function authenticate(TokenInterface $token)
    {
        return $token;
    }

    public function supports(TokenInterface $token)
    {
        return false; // All of this is just here to add a option to the firewall config.
    }
}
