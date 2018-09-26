<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\FormConfig\Util;

class KeyGenerator
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function generate(string $name): string
    {
        return preg_replace('#[^a-z1-9]+#u', '_', strtolower($name));
    }
}
