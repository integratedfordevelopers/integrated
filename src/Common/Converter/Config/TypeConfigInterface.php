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
interface TypeConfigInterface
{
    /**
     * Get the type name.
     *
     * @return string
     */
    public function getName();

    /**
     * Check if the type has options.
     *
     * @return bool
     */
    public function hasOptions();

    /**
     * Get the type options.
     *
     * @return null | array
     */
    public function getOptions();
}
