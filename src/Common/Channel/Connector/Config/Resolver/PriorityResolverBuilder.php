<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Config\Resolver;

use Integrated\Common\Channel\Connector\Config\ResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityResolverBuilder
{
    /**
     * @var array
     */
    private $resolvers = [];

    /**
     * @param ResolverInterface $resolver
     * @param int               $priority
     *
     * @return $this
     */
    public function addResolver(ResolverInterface $resolver, $priority = 0)
    {
        $this->removeResolver($resolver);
        $this->resolvers[$priority][] = $resolver;

        return $this;
    }

    /**
     * @param ResolverInterface[] $resolvers
     * @param int                 $priority
     *
     * @return $this
     */
    public function addResolvers(array $resolvers, $priority = 0)
    {
        foreach ($resolvers as $resolver) {
            $this->addResolver($resolver, $priority);
        }

        return $this;
    }

    protected function removeResolver(ResolverInterface $resolver)
    {
        foreach ($this->resolvers as $priority => $resolvers) {
            if (false !== ($key = array_search($resolver, $resolvers, true))) {
                unset($this->resolvers[$priority][$key]);
            }
        }
    }

    /**
     * @return PriorityResolver
     */
    public function getResolver()
    {
        krsort($this->resolvers);

        return new PriorityResolver(\call_user_func_array('array_merge', $this->resolvers));
    }
}
