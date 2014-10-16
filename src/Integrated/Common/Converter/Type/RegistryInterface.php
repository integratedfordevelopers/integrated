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
     * @param string $name
     * @return bool
     */
    public function hasType($name);

    /**
     * @param string $name
     * @return ResolvedTypeInterface
     *
     * @trows InvalidArgumentException if type can not be found
     */
    public function getType($name);
}
