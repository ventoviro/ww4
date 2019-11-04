<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Utilities;

use Windwalker\Utilities\Classes\PreventInitialTrait;

/**
 * The TypeCast class.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class TypeCast
{
    use PreventInitialTrait;

    /**
     * Utility function to convert all types to an array.
     *
     * @param  mixed  $data       The data to convert.
     * @param  bool   $recursive  Recursive if data is nested.
     *
     * @return  array  The converted array.
     */
    public static function toArray($data, bool $recursive = false): array
    {
        // Ensure the input data is an array.
        if ($data instanceof \Traversable) {
            $data = iterator_to_array($data);
        } elseif (is_object($data)) {
            $data = get_object_vars($data);
        } else {
            $data = (array) $data;
        }

        if ($recursive) {
            foreach ($data as &$value) {
                if (is_array($value) || is_object($value)) {
                    $value = static::toArray($value, $recursive);
                }
            }
        }

        return $data;
    }

    /**
     * toIterable
     *
     * @param  mixed  $iterable
     *
     * @return  iterable
     *
     * @since  3.5
     */
    public static function toIterable($iterable): iterable
    {
        if (is_iterable($iterable)) {
            return $iterable;
        }

        if (is_object($iterable)) {
            return get_object_vars($iterable);
        }

        return (array) $iterable;
    }

    /**
     * Utility function to map an array to a stdClass object.
     *
     * @param  array   $array  The array to map.
     * @param  string  $class  Name of the class to create
     *
     * @return  object  The object mapped from the given array
     *
     * @since   2.0
     */
    public static function toObject(array $array, $class = \stdClass::class)
    {
        $obj = new $class();

        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $obj->$k = static::toObject($v, $class);
            } else {
                $obj->$k = $v;
            }
        }

        return $obj;
    }
}
