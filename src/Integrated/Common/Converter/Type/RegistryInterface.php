<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Type;

use Integrated\Common\Converter\Exception\RuntimeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface RegistryInterface
{
    /**
     * Check if there is a type with the $name in the registry
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasType($name);

    /**
     * Get the type with the $name from the registry
     *
     * @param string $name
     *
     * @return ResolvedTypeInterface
     *
     * @trows InvalidArgumentException if the type can not be found
     */
    public function getType($name);
}
