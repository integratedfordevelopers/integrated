<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\Diff;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ArrayComparer
{
    /**
     * @param array $old
     * @param array $new
     * @return array
     */
    public static function diff(array $old = [], array $new = [])
    {
        $diff = [];

        foreach ($old as $key => $value) {
            if (!array_key_exists($key, $new)) {
                // key is missing in the new array
                $diff[$key] = null;
            }
        }

        foreach ($new as $key => $value) {
            if (array_key_exists($key, $old)) {
                if ($old[$key] != $value) {
                    // value has changed
                    $diff[$key] = $value;
                }
            } elseif (null !== $value) {
                // value is new
                $diff[$key] = $value;
            }
        }

        return $diff;
    }
}
