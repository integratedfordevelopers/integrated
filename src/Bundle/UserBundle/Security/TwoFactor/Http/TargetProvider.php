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

use Symfony\Component\Security\Http\Util\TargetPathTrait;

class TargetProvider
{
    use TargetPathTrait {
        TargetPathTrait::getTargetPath as getTargetPathPrivate;
    }

    /**
     * @var bool[]
     */
    private $alwaysUseDefault;

    public function __construct(array $alwaysUseDefault = [])
    {
        $this->alwaysUseDefault = $alwaysUseDefault;
    }

    public function getTargetPath(Context $context): string
    {
        if ($this->alwaysUseDefault[$context->getFirewall()] ?? false) {
            return $context->getConfig()->getTargetPath();
        }

        if ($target = $this->getTargetPathPrivate($context->getSession(), $context->getFirewall())) {
            $this->removeTargetPath($context->getSession(), $context->getFirewall());

            return $target;
        }

        return $context->getConfig()->getTargetPath();
    }
}
