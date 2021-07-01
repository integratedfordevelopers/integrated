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

class WhitelistProvider implements WhitelistProviderInterface
{
    /**
     * @var WhitelistMatcherInterface
     */
    private $matcher;

    public function __construct(WhitelistMatcherInterface $matcher)
    {
        $this->matcher = $matcher;
    }

    public function getMatcher(Context $context): WhitelistMatcherInterface
    {
        return $this->matcher;
    }
}
