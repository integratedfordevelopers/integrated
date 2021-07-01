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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class WhitelistMatcher implements WhitelistMatcherInterface
{
    /**
     * @var RequestMatcherInterface[]
     */
    private $matchers;

    public function __construct(array $matchers)
    {
        $this->matchers = $matchers;
    }

    public function isWhitelisted(Request $request): bool
    {
        foreach ($this->matchers as $matcher) {
            if ($matcher->matches($request)) {
                return true;
            }
        }

        return false;
    }
}
