<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Security;

use Darsyn\IP\Exception\IpException;
use Darsyn\IP\Version\Multi as IP;
use Integrated\Bundle\UserBundle\Model\IpListManagerInterface;
use Integrated\Common\Security\IpListMatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class IpListMatcher implements IpListMatcherInterface
{
    /**
     * @var IpListManagerInterface
     */
    private $manager;

    public function __construct(IpListManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function match(Request $request): bool
    {
        try {
            $ip = IP::factory($request->getClientIp());
        } catch (IpException $e) {
            return false;
        }

        if ($this->manager->findBy(['ip' => $ip])) {
            return true;
        }

        return false;
    }
}
