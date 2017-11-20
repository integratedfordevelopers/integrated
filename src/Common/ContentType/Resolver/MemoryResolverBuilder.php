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

use Integrated\Common\ContentType\ContentTypeInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MemoryResolverBuilder
{
    /**
     * @var ContentTypeInterface[]
     */
    private $types = [];

    /**
     * Add the content type to the builder.
     *
     * @param ContentTypeInterface $type
     *
     * @return MemoryResolverBuilder
     */
    public function addContentType(ContentTypeInterface $type)
    {
        $this->types[$type->getId()] = $type;

        return $this;
    }

    /**
     * Add the content types to the builder.
     *
     * @param ContentTypeInterface[] $types
     *
     * @return MemoryResolverBuilder
     */
    public function addContentTypes(array $types)
    {
        foreach ($types as $type) {
            $this->addContentType($type);
        }

        return $this;
    }

    /**
     * Create a resolver from the current builder configuration.
     *
     * @return MemoryResolver
     */
    public function getResolver()
    {
        return new MemoryResolver($this->types);
    }
}
