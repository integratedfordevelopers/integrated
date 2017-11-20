<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Config;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface TypeProviderInterface
{
    /**
     * Get the type config for the $class.
     *
     * @param string $class
     *
     * @return TypeConfigInterface[]
     */
    public function getTypes($class);
}
