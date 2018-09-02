<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Resolver;

use Integrated\Common\ContentType\ResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityResolverBuilder
{
    /**
     * @var ResolverInterface[][]
     */
    private $resolvers = [];

    /**
     * Add a resolver to the builder.
     *
     * If the resolver is already in the list then if will first be removed and
     * then added with the new priority.
     *
     * @param ResolverInterface $resolver
     * @param int               $priority
     *
     * @return PriorityResolverBuilder
     */
    public function addResolver(ResolverInterface $resolver, $priority = 0)
    {
        foreach ($this->resolvers as $index => $resolvers) {
            if (false !== ($key = array_search($resolver, $resolvers, true))) {
                unset($this->resolvers[$index][$key]);
            }
        }

        $this->resolvers[$priority][] = $resolver;

        return $this;
    }

    /**
     * Create a resolver from the current builder configuration.
     *
     * @return PriorityResolver
     */
    public function getResolver()
    {
        $resolvers = $this->resolvers;

        if (!empty($resolvers)) {
            krsort($resolvers);
            $resolvers = \call_user_func_array('array_merge', $resolvers);
        }

        return new PriorityResolver($resolvers);
    }
}
