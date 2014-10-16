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
     * @return string
     */
    public function getName();

    /**
     * @return bool
     */
    public function hasOptions();

    /**
     * @return null | array
     */
    public function getOptions();
}
