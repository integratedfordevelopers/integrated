<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Doctrine\ODM\MongoDB\Mapping;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface DiscriminatorMapResolverInterface
{
    /**
     * Resolve the discriminator map for the given class.
     *
     * @param string $class
     *
     * @return string[]
     */
    public function resolve($class);
}
