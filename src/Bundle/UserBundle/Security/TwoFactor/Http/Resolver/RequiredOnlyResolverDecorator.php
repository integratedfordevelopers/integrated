<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Security\TwoFactor\Http\Resolver;

use Integrated\Bundle\UserBundle\Security\TwoFactor\Http\Context;
use Integrated\Bundle\UserBundle\Security\TwoFactor\Http\ContextResolverInterface;
use Symfony\Component\HttpFoundation\Request;

class RequiredOnlyResolverDecorator implements ContextResolverInterface
{
    /**
     * @var ContextResolverInterface
     */
    private $resolver;

    public function __construct(ContextResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function resolve(Request $request): ?Context
    {
        $context = $this->resolver->resolve($request);

        if ($context && $context->getConfig()->isRequired()) {
            return $context;
        }

        return null;
    }
}
