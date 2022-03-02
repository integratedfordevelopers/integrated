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
    public static function diff($old, $new)
    {
        $oldOriginal = $old;
        $newOriginal = $new;
//        var_dump('input1');
//        var_dump($old);

//        var_dump('input2');
//        var_dump($new);

        $old = self::normalizeValue($old);
        $new = self::normalizeValue($new);

//exit;
        if (!is_array($old) || !is_array($new)) {
            var_dump('early return');
            if (self::isSame($old, $new)) {
                return [];
            }

            return [$oldOriginal, $newOriginal];
        }

        $old = self::normalizeArrays($new, $old);
        $new = self::normalizeArrays($old, $new);

        var_dump('input1 norm');
        var_dump($old);

        var_dump('input2 norm');
        var_dump($new);

        $diff = [];

        foreach ($new as $key => $value) {
            var_dump($value);
            if (\in_array($key, self::IGNORE_KEYS)) {
                continue;
            }

            if (\is_array($value)) {
                $result = self::diff($old[$key], $value);
                if (\count($result)) {
                    $diff[$key] = [$result[0], $result[1]];
                }
            } elseif (!self::isSame($old[$key], $value)) {
                // value has changed
                var_dump($key.' - '.$old[$key].'-'.$value."\n");
                $diff[$key] = [$old[$key], $value];
            }
        }

        //var_dump('return');
        //var_dump($diff);
        return $diff;
    }

    /**
     * @param array $old
     * @param array $new
     *
     * @return array
     */
    public static function normalizeArrays(array $old = [], array $new = [])
    {
        foreach (array_keys($old) as $key) {
            //if (is_object($new[$key])) {
            //    $new[$key] = (array) $new[$key];
            //}
            if (!\array_key_exists($key, $new)) {
                // add missing key
                if (\is_array($old[$key])) {
                    $new[$key] = [];
                } else {
                    $new[$key] = null;
                }
            }

            if (\is_array($old[$key]) && !\is_array($new[$key])) {
                $new[$key] = [];
            }
        }

        return $new;
    }

    public static function normalizeValue($value, bool $allowArray = true)
    {
        if (is_object($value)) {
            $value = serialize((array) $value);
        }

        if (!$allowArray && is_array($value)) {
            $value = serialize($value);
        }

        return $value;
    }

    public static function isSame($value1, $value2): bool
    {
        //var_dump('compare');
        //var_dump($value1);
        //var_dump($value2);
        $value1 = (string) self::normalizeValue($value1, false);
        $value2 = (string) self::normalizeValue($value2, false);

        //var_dump($value1 === $value2);
        return ($value1 === $value2);
    }
}
