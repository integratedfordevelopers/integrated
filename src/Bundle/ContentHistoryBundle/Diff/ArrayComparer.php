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
     *
     * @return array
     */
    public static function diff(array $old = [], array $new = [])
    {
        foreach (array_keys($old) as $key) {
            if (!\array_key_exists($key, $new)) {
                // add missing key
                $new[$key] = null;
            }
        }

        foreach (array_keys($new) as $key) {
            if (!\array_key_exists($key, $old)) {
                // add missing key
                $old[$key] = null;
            }
        }

        $diff = [];

        foreach ($new as $key => $value) {
            if ($old[$key] != $value) {
                // value has changed
                $diff[$key] = [$old[$key], $value];
            }
        }

        return $diff;
    }
}
