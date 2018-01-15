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

use Integrated\Common\ContentType\Exception\InvalidArgumentException;
use Integrated\Common\ContentType\ResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityResolver implements ResolverInterface
{
    /**
     * @var ResolverInterface[]
     */
    private $resolvers;

    /**
     * Constructor.
     *
     * @param ResolverInterface[] $resolvers
     */
    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * Check is a resolver is added to the priority list.
     *
     * @param ResolverInterface $resolver The resolver to check
     *
     * @return bool
     */
    public function hasResolver(ResolverInterface $resolver)
    {
        return (bool) (false !== array_search($resolver, $this->resolvers, true));
    }

    /**
     * Get all the registered resolvers.
     *
     * @return ResolverInterface[]
     */
    public function getResolvers()
    {
        return $this->resolvers;
    }

    /**
     * Find the first resolver that has the requested type.
     *
     * @param string $type
     *
     * @return ResolverInterface
     */
    private function findResolver($type)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->hasType($type)) {
                return $resolver;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($type)
    {
        if ($resolver = $this->findResolver($type)) {
            return $resolver->getType($type);
        }

        throw new InvalidArgumentException(sprintf('Could not resolve the content type based on the given type "%s"', $type));
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($type)
    {
        if ($this->findResolver($type)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return new PriorityIterator($this->resolvers);
    }
}
