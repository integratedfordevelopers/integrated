<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Mapping;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface MetadataFactoryInterface
{
    /**
     * @param string $class
     * @return Metadata
     */
    public function getMetadata($class);
}
