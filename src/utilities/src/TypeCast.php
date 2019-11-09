<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Utilities;

use Windwalker\Utilities\Classes\PreventInitialTrait;
use Windwalker\Utilities\Contract\DumpableInterface;

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
     * @param  mixed  $data          The data to convert.
     * @param  bool   $recursive     Recursive if data is nested.
     * @param  bool   $onlyDumpable  Objects only implements DumpableInterface will convert to array.
     *
     * @return  array  The converted array.
     */
    public static function toArray($data, bool $recursive = false, bool $onlyDumpable = false): array
    {
        // Ensure the input data is an array.
        if ($data instanceof DumpableInterface) {
            $data = $data->dump($recursive);

            if ($recursive) {
                return $data;
            }
        } elseif ($data instanceof \Traversable) {
            $data = iterator_to_array($data);
        } elseif (is_object($data)) {
            $data = get_object_vars($data);
        } else {
            $data = (array) $data;
        }

        if ($recursive) {
            foreach ($data as &$value) {
                if (is_array($value)) {
                    $value = static::toArray($value, $recursive, $onlyDumpable);
                } elseif (is_object($value)) {
                    if ($onlyDumpable && $value instanceof DumpableInterface) {
                        $value = static::toArray($value, $recursive, $onlyDumpable);
                    } elseif (!$onlyDumpable) {
                        $value = static::toArray($value, $recursive, $onlyDumpable);
                    }
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
     * @param  array   $array      The array to map.
     * @param  bool    $recursive  Recursive.
     * @param  string  $class      Name of the class to create
     *
     * @return  object  The object mapped from the given array
     *
     * @since   2.0
     */
    public static function toObject(array $array, bool $recursive = false, string $class = \stdClass::class)
    {
        $obj = new $class();

        foreach ($array as $k => $v) {
            if (is_array($v) && $recursive) {
                $obj->$k = static::toObject($v, $recursive, $class);
            } else {
                $obj->$k = $v;
            }
        }

        return $obj;
    }

    /**
     * Convert all to string.
     *
     * @param mixed $data The data to convert.
     * @param bool  $dump If is array or object, will dump it if this argument set to TRUE.
     *
     * @return  string
     *
     * @since  3.5
     */
    public static function toString($data, bool $dump = true): string
    {
        if (is_callable($data)) {
            return static::toString($data());
        }

        if (is_stringable($data)) {
            return (string) $data;
        }

        if (is_array($data)) {
            $data = $dump ? Arr::dump($data) : 'Array()';
        }

        if (is_object($data)) {
            $data = $dump ? Arr::dump($data) : sprintf('[Object %s]', get_class($data));
        }

        return (string) $data;
    }

    /**
     * mapAs
     *
     * @param  array   $src
     * @param  string  $typeOrClass
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function mapAs(array $src, string $typeOrClass): array
    {
        if ($typeOrClass === 'array') {
            return array_map(static fn ($value) => TypeCast::toArray($value), $src);
        }

        if ($typeOrClass === 'string') {
            return array_map(static fn ($value) => (string) $value, $src);
        }

        if ($typeOrClass === 'int' || $typeOrClass === 'integer') {
            return array_map(static fn ($value) => (int) $value, $src);
        }

        if ($typeOrClass === 'float' || $typeOrClass === 'double') {
            return array_map(static fn ($value) => (float) $value, $src);
        }

        if ($typeOrClass === 'bool' || $typeOrClass === 'boolean') {
            return array_map(static fn ($value) => (bool) $value, $src);
        }

        if (class_exists($typeOrClass)) {
            return array_map(static fn ($value) => new $typeOrClass($value), $src);
        }

        throw new \InvalidArgumentException(sprintf('Class %s not exists', $typeOrClass));
    }
}
