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

use Integrated\Common\ContentType\ContentTypeResolverInterface;
use Integrated\Common\ContentType\Exception\InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityResolver implements ContentTypeResolverInterface
{
    /**
     * @var ContentTypeResolverInterface[]
     */
    private $resolvers;

    /**
     * Constructor.
     *
     * @param ContentTypeResolverInterface[] $resolvers
     */
    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * Check is a resolver is added to the priority list.
     *
     * @param ContentTypeResolverInterface $resolver The resolver to check
     *
     * @return bool
     */
    public function hasResolver(ContentTypeResolverInterface $resolver)
    {
        foreach ($this->resolvers as $resolvers) {
            if (false !== array_search($resolver, $resolvers, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all the registered resolvers.
     *
     * @return ContentTypeResolverInterface[]
     */
    public function getResolvers()
    {
        return $this->resolvers;
    }

    /**
     * Find the first resolver that has the requested type.
     *
     * @param string $class
     * @param string $type
     *
     * @return ContentTypeResolverInterface
     */
    private function findResolver($class, $type)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->hasType($class, $type)) {
                return $resolver;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($class, $type)
    {
        if ($resolver = $this->findResolver($class, $type)) {
            return $resolver->getType($class, $type);
        }

        throw new  InvalidArgumentException(sprintf('Could not resolve the content type based on the given class "%s" and type "%s"', $class, $type));
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($class, $type)
    {
        if ($this->findResolver($class, $type)) {
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
