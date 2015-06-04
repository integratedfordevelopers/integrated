<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FilterContainer extends Container
{
    /**
     * {@inheritdoc}
     */
    public function add($key, $value)
    {
        if (is_string($value)) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        return parent::add($key, $value);
    }
}
