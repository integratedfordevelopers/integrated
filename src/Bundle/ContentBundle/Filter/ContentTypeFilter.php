<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Filter;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ContentTypeFilter
{
    public const FILTER_IMAGE = 'image';
    public const FILTER_VIDEO = 'video';

    /**
     * @param string      $class
     * @param string|null $filter
     *
     * @return bool
     */
    public static function match($class, $filter)
    {
        if (null === $filter) {
            return true;
        }

        return $filter == strtolower(substr($class, strrpos($class, '\\') + 1));
    }
}
