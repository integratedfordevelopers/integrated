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

use Integrated\Bundle\UserBundle\Security\TwoFactor\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface ContextFactoryInterface
{
    public function create(Request $request, TokenInterface $token, string $firewall, Config $config): ContextInterface;
}
