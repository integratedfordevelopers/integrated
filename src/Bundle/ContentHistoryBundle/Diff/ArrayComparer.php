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
    const IGNORE_KEYS = ['$db'];

    /**
     * @param array $old
     * @param array $new
     *
     * @return array
     */
    public static function diff(array $old = [], array $new = [])
    {
        $old = self::normalize($new, $old);

        $new = self::normalize($old, $new);

        $diff = [];

        foreach ($new as $key => $value) {
            if (\in_array($key, self::IGNORE_KEYS)) {
                continue;
            }

            if (\is_array($value) && null !== $old[$key]) {
                $result = self::diff($old[$key], $value);
                if (\count($result)) {
                    $diff[$key] = $result;
                }
            } elseif ($old[$key] !== $value) {
                // value has changed
                $diff[$key] = [$old[$key], $value];
            }
        }

        return $diff;
    }

    /**
     * @param array $old
     * @param array $new
     *
     * @return array
     */
    public static function normalize(array $old = [], array $new = [])
    {
        foreach (array_keys($old) as $key) {
            if (!\array_key_exists($key, $new)) {
                // add missing key
                if (\is_array($old[$key])) {
                    $new[$key] = [];
                } else {
                    $new[$key] = null;
                }
            }

            if (\is_array($old[$key])) {
                $new[$key] = self::normalize($old[$key], $new[$key]);
            }
        }

        return $new;
    }
}
