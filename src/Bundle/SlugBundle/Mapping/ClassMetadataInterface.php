<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Mapping;

interface ClassMetadataInterface
{
    /**
     * @return PropertyMetadataInterface[]
     */
    public function getProperties(): array;

    /**
     * @param PropertyMetadataInterface[] $properties
     */
    public function setProperties(array $properties): void;

    public function addProperty(PropertyMetadataInterface $property): void;
}
