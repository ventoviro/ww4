<?php declare(strict_types=1);
/**
 * Part of phoenix project.
 *
 * @copyright  Copyright (C) 2019 $Asikart.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Utilities;

/**
 * The ArrConverter class.
 *
 * @since  3.5
 */
class ArrConverter
{
    public const GROUP_TYPE_ARRAY = 1;

    public const GROUP_TYPE_KEY_BY = 2;

    public const GROUP_TYPE_MIX = 3;

    /**
     * Flip array key and value and make 2-dimensional array to 1-dimensional.
     *
     * Similar to array_flip, but it handles 2-dimensional array.
     *
     * @param  array  $array
     *
     * @return  array
     *
     * @since  3.5.11
     */
    public static function flipMatrix(array $array): array
    {
        $new = [];

        foreach ($array as $k => $v) {
            foreach ($v as $k2 => $v2) {
                $new[$v2] = $k;
            }
        }

        return $new;
    }

    /**
     * Transpose a two-dimensional matrix array.
     *
     * @param  array  $array  An array with two level.
     *
     * @return array An pivoted array.
     */
    public static function transpose(array $array): array
    {
        $new  = [];
        $keys = array_keys($array);

        foreach ($keys as $k => $val) {
            foreach ((array) $array[$val] as $k2 => $v2) {
                $new[$k2][$val] = $v2;
            }
        }

        return $new;
    }

    /**
     * Same as ArrayHelper::pivot().
     * From:
     *          [0] => Array
     *             (
     *                 [value] => aaa
     *                 [text] => aaa
     *             )
     *         [1] => Array
     *             (
     *                 [value] => bbb
     *                 [text] => bbb
     *             )
     * To:
     *         [value] => Array
     *             (
     *                 [0] => aaa
     *                 [1] => bbb
     *             )
     *         [text] => Array
     *             (
     *                 [0] => aaa
     *                 [1] => bbb
     *             )
     *
     * @param  array  $array  An array with two level.
     *
     * @return  array An pivoted array.
     *
     * @deprecated  Use transpose()
     */
    public static function pivotBySort(array $array): array
    {
        $new    = [];
        $array2 = $array;
        $first  = array_shift($array2);

        foreach ($array as $k => $v) {
            foreach ((array) $first as $k2 => $v2) {
                $new[$k2][$k] = $array[$k][$k2];
            }
        }

        return $new;
    }

    /**
     * Pivot $origin['prefix_xxx'] to $target['prefix']['xxx'].
     *
     * @param  string  $prefix  A prefix text.
     * @param  array   $origin  Origin array to pivot.
     * @param  array   $target  A target array to store pivoted value.
     *
     * @return  array  Pivoted array.
     */
    public static function groupPrefix(string $prefix, $origin, $target = null)
    {
        $target = is_object($target) ? (object) $target : (array) $target;

        foreach ((array) $origin as $key => $row) {
            if (strpos($key, $prefix) === 0) {
                $key2   = mb_substr($key, mb_strlen($prefix));
                $target = Arr::set($target, $key2, $row);
            }
        }

        return $target;
    }

    /**
     * Pivot $origin['prefix']['xxx'] to $target['prefix_xxx'].
     *
     * @param  string  $prefix  A prefix text.
     * @param  array   $origin  Origin array to pivot.
     * @param  array   $target  A target array to store pivoted value.
     *
     * @return  array  Pivoted array.
     */
    public static function extractPrefix(string $prefix, $origin, $target = null)
    {
        $target = is_object($target) ? (object) $target : (array) $target;

        foreach ((array) $origin as $key => $val) {
            $key = $prefix . $key;

            if (!Arr::get($target, $key)) {
                $target = Arr::set($target, $key, $val);
            }
        }

        return $target;
    }

    /**
     * Pivot two-dimensional array to one-dimensional.
     *
     * @param  array|object &$array  A two-dimension array.
     *
     * @return  array  Pivoted array.
     */
    public static function pivotFromTwoDimension($array)
    {
        $new = [];

        foreach ((array) $array as $val) {
            if (is_array($val) || is_object($val)) {
                foreach ((array) $val as $key => $val2) {
                    $new = Arr::set($new, $key, $val2);
                }
            }
        }

        return $new;
    }

    /**
     * Pivot one-dimensional array to two-dimensional array by a key list.
     *
     * @param  array|object &$array  Array to pivot.
     * @param  array         $keys   The fields' key list.
     *
     * @return  array  Pivoted array.
     */
    public static function pivotToTwoDimension($array, array $keys = [])
    {
        $new = [];

        foreach ($keys as $key) {
            if (is_object($array)) {
                $array2 = clone $array;
            } else {
                $array2 = $array;
            }

            $new = Arr::set($new, $key, $array2);
        }

        return $new;
    }

    /**
     * Re-group an array to create a reverse lookup of an array of scalars, arrays or objects.
     *
     * @param  array   $array  The source array data.
     * @param  string  $key    Where the elements of the source array are objects or arrays, the key to pivot on.
     * @param  int     $type   The group type.
     *
     * @return array An array of arrays grouped either on the value of the keys,
     *               or an individual key of an object or array.
     *
     * @since  2.0
     */
    public static function group(array $array, ?string $key = null, int $type = self::GROUP_TYPE_ARRAY): array
    {
        $results  = [];
        $hasArray = [];

        foreach ($array as $index => $value) {
            // List value
            if (is_array($value) || is_object($value)) {
                if (!Arr::has($value, $key)) {
                    continue;
                }

                $resultKey   = Arr::get($value, $key);
                $resultValue = $array[$index];
            } else {
                // Scalar value.
                $resultKey   = $value;
                $resultValue = $index;
            }

            // First set value if not exists.
            if (!isset($results[$resultKey])) {
                // Force first element in array.
                if ($type === static::GROUP_TYPE_ARRAY) {
                    $results[$resultKey]  = [$resultValue];
                    $hasArray[$resultKey] = true;
                } else {
                    // Keep first element single.
                    $results[$resultKey] = $resultValue;
                }
            } elseif ($type === static::GROUP_TYPE_MIX && empty($hasArray[$resultKey])) {
                // If first element is single, now add second element as an array.
                $results[$resultKey] = [
                    $results[$resultKey],
                    $resultValue,
                ];

                $hasArray[$resultKey] = true;
            } elseif ($type === static::GROUP_TYPE_KEY_BY) {
                $results[$resultKey] = $resultValue;
            } else {
                // Now always push new elements.
                $results[$resultKey][] = $resultValue;
            }
        }

        unset($hasArray);

        return $results;
    }
}
