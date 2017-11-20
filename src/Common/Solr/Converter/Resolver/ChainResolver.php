<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Converter\Resolver;

use Integrated\Common\Solr\Converter\ConverterSpecificationInterface;
use Integrated\Common\Solr\Converter\ConverterSpecificationResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChainResolver implements ConverterSpecificationResolverInterface
{
    /**
     * @var ConverterSpecificationResolverInterface[]
     */
    private $resolvers = array();

    /**
     * Add a resolver to the list
     *
     * @param ConverterSpecificationResolverInterface $resolver
     */
    public function addResolver(ConverterSpecificationResolverInterface $resolver)
    {
        if (!$this->hasResolver($resolver)) {
            $this->resolvers[] = $resolver;
        }
    }

    /**
     * Check is a resolver is added to the list
     *
     * @param ConverterSpecificationResolverInterface $resolver The resolver to check
     * @return bool
     */
    public function hasResolver(ConverterSpecificationResolverInterface $resolver)
    {
        if (false !== array_search($resolver, $this->resolvers, true)) {
            return true;
        }

        return false;
    }

    /**
     * Remove a resolver from the priority list
     *
     * @param ConverterSpecificationResolverInterface $resolver The resolver to remove
     */
    public function removeResolver(ConverterSpecificationResolverInterface $resolver)
    {
        if (false !== ($key = array_search($resolver, $this->resolvers, true))) {
            unset($this->resolvers[$key]);
        }
    }

    /**
     * Get all the registered resolvers
     *
     * @return ConverterSpecificationResolverInterface[]
     */
    public function getResolvers()
    {
        return $this->resolvers;
    }

    /**
     * Clear all the resolvers
     */
    public function clearResolvers()
    {
        $this->resolvers = array();
    }

    /**
     * @param $class
     * @return bool
     */
    public function hasSpecification($class)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->hasSpecification($class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $class
     * @return ConverterSpecificationInterface
     */
    public function getSpecification($class)
    {
        foreach ($this->resolvers as $resolver) {
            if (null !== ($spec = $resolver->getSpecification($class))) {
                return $spec;
            }
        }

        return null;
    }
}
