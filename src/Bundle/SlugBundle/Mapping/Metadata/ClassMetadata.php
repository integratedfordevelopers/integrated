<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Mapping\Metadata;

use Integrated\Bundle\SlugBundle\Mapping\ClassMetadataInterface;
use Integrated\Bundle\SlugBundle\Mapping\PropertyMetadataInterface;

class ClassMetadata implements ClassMetadataInterface
{
    /**
     * @var PropertyMetadataInterface[]
     */
    private $properties = [];

    /**
     * @return PropertyMetadataInterface[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param PropertyMetadataInterface[] $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = [];
        foreach ($properties as $property) {
            $this->addProperty($property);
        }
    }

    public function addProperty(PropertyMetadataInterface $property): void
    {
        $this->properties[$property->getName()] = $property;
    }
}
