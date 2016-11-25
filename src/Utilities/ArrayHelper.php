<?php
/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT.
 * @license    Please see LICENSE file.
 */
declare(strict_types=1);

namespace Windwalker\Utilities;

/**
 * The ArrayHelper class
 *
 * @since  2.0
 */
class ArrayHelper
{
    /**
     * Utility function to convert all types to an array.
     *
     * @param   mixed  $data       The data to convert.
     * @param   bool   $recursive  Recursive if data is nested.
     *
     * @return  array  The converted array.
     */
    public static function toArray($data, $recursive = false)
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
     * Check a key exists in object or array. The key can be a path separated by dots.
     *
     * @param array|object $array     Object or array to check.
     * @param string       $key       The key path name.
     * @param string       $delimiter The separator to split paths.
     *
     * @return  bool
     *
     * @since 4.0
     */
    public static function has($array, $key, string $delimiter = '.') : bool
    {
        $nodes = array_values(array_filter(explode($delimiter, (string) $key), 'strlen'));

        if (empty($nodes)) {
            return false;
        }

        $dataTmp = $array;

        foreach ($nodes as $arg) {
            if (is_object($dataTmp) && property_exists($dataTmp, $arg)) {
                // Check object value exists
                $dataTmp = $dataTmp->$arg;
            } elseif ($dataTmp instanceof \ArrayAccess && isset($dataTmp[$arg])) {
                // Check arrayAccess value exists
                $dataTmp = $dataTmp[$arg];
            } elseif (is_array($dataTmp) && array_key_exists($arg, $dataTmp)) {
                // Check object value exists
                $dataTmp = $dataTmp[$arg];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Set a default value to array or object if not exists. Key can be a path separated by dots.
     *
     * @param array|object $array     Object or array to set default value.
     * @param string       $key       Key path name.
     * @param mixed        $value     Value to set if not exists.
     * @param string       $delimiter Separator to split paths.
     *
     * @return  array|object
     * @throws \InvalidArgumentException
     *
     * @since 4.0
     */
    public static function def($array, $key, $value, string $delimiter = '.')
    {
        if (static::has($array, $key, $delimiter)) {
            return $array;
        }

        return static::set($array, $key, $value, $delimiter);
    }

    /**
     * Get data from array or object by path.
     *
     * Example: `ArrayHelper::get($array, 'foo.bar.yoo')` equals to $array['foo']['bar']['yoo'].
     *
     * @param mixed  $data      An array or object to get value.
     * @param string $key       The key path.
     * @param mixed  $default   The default value if not exists.
     * @param string $delimiter Separator of paths.
     *
     * @return mixed Found value, null if not exists.
     *
     * @since   2.0
     */
    public static function get($data, $key, $default = null, string $delimiter = '.')
    {
        $nodes = array_values(array_filter(explode($delimiter, (string) $key), 'strlen'));

        if (empty($nodes)) {
            return $default;
        }

        $dataTmp = $data;

        foreach ($nodes as $arg) {
            if ($dataTmp instanceof \ArrayAccess && isset($dataTmp[$arg])) {
                // Check arrayAccess value exists
                $dataTmp = $dataTmp[$arg];
            } elseif (is_object($dataTmp) && isset($dataTmp->$arg)) {
                // Check object value exists
                $dataTmp = $dataTmp->$arg;
            } elseif (is_array($dataTmp) && isset($dataTmp[$arg])) {
                // Check object value exists
                $dataTmp = $dataTmp[$arg];
            } else {
                return $default;
            }
        }

        return $dataTmp;
    }

    /**
     * Set value into array or object. The key can be path type.
     *
     * @param mixed  $data      An array or object to set data.
     * @param string $key       Path name separate by dot.
     * @param mixed  $value     Value to set into array or object.
     * @param string $delimiter Separator to split path.
     * @param string $storeType The new store data type, default is `array`. you can set object class name.
     *
     * @return  array|object
     * @throws \InvalidArgumentException
     *
     * @since   2.0
     */
    public static function set($data, $key, $value, string $delimiter = '.', string $storeType = 'array')
    {
        $nodes = array_values(array_filter(explode($delimiter, (string) $key), 'strlen'));

        if (empty($nodes)) {
            return $data;
        }

        /**
         * A closure as inner function to create data store.
         *
         * @param string $type Type name.
         *
         * @return  array
         *
         * @throws \InvalidArgumentException
         */
        $createStore = function (string $type) {
            if (strtolower($type) === 'array') {
                return array();
            }

            if (class_exists($type)) {
                return new $type;
            }

            throw new \InvalidArgumentException(sprintf('Type or class: %s not exists', $type));
        };

        $dataTmp = &$data;

        foreach ($nodes as $node) {
            if (is_object($dataTmp)) {
                // If this node not exists, create new one.
                if (empty($dataTmp->$node)) {
                    $dataTmp->$node = $createStore($storeType);
                }

                $dataTmp = &$dataTmp->$node;
            } elseif (is_array($dataTmp)) {
                // If this node not exists, create new one.
                if (empty($dataTmp[$node])) {
                    $dataTmp[$node] = $createStore($storeType);
                }

                $dataTmp = &$dataTmp[$node];
            } else {
                // If a node is value but path is not go to the end, we replace this value as a new store.
                // Then next node can insert new value to this store.
                $dataTmp = &$createStore($storeType);
            }
        }

        // Now, path go to the end, means we get latest node, set value to this node.
        $dataTmp = $value;

        return $data;
    }

    /**
     * Remove a value from array or object. The key can be a path separated by dots.
     *
     * @param array|object $data      Object or array to remove value.
     * @param string       $key       The key path name.
     * @param string       $delimiter The separator to split paths.
     *
     * @return  array|object
     */
    public static function remove($data, $key, string $delimiter = '.')
    {
        $nodes = array_values(array_filter(explode($delimiter, (string) $key), 'strlen'));

        if (!count($nodes)) {
            return $data;
        }

        $previous = null;
        $dataTmp  = &$data;

        foreach ($nodes as $node) {
            if (is_object($dataTmp)) {
                if (empty($dataTmp->$node)) {
                    return $data;
                }

                $previous = &$dataTmp;
                $dataTmp  = &$dataTmp->$node;
            } elseif (is_array($dataTmp)) {
                if (empty($dataTmp[$node])) {
                    return $data;
                }

                $previous = &$dataTmp;
                $dataTmp  = &$dataTmp[$node];
            } else {
                return $data;
            }
        }

        // Now, path go to the end, means we get latest node, unset value to this node.
        if (is_object($previous)) {
            unset($previous->$node);
        } elseif (is_array($previous)) {
            unset($previous[$node]);
        }

        return $data;
    }

    /**
     * Collapse array to one dimension
     *
     * @param   array|object $data
     *
     * @return  array
     */
    public static function collapse($data)
    {
        return array_values(static::flatten($data, '.', 2));
    }

    /**
     * Method to recursively convert data to one dimension array.
     *
     * @param   array|object $array     The array or object to convert.
     * @param   string       $delimiter The key path delimiter.
     * @param   int          $depth     Only flatten limited depth, 0 means on limit.
     * @param   string       $prefix    Last level key prefix.
     *
     * @return array
     */
    public static function flatten($array, string $delimiter = '.', int $depth = 0, string $prefix = '')
    {
        $temp = [];

        if ($array instanceof \Traversable) {
            $array = iterator_to_array($array);
        } elseif (is_object($array)) {
            $array = get_object_vars($array);
        }

        foreach ($array as $k => $v) {
            $key = $prefix ? $prefix . $delimiter . $k : $k;

            if (($depth === 0 || $depth > 1) && (is_object($v) || is_array($v))) {
                if ($depth === 0) {
                    $temp[] = static::flatten($v, $delimiter, 0, (string) $key);
                } else {
                    $temp[] = static::flatten($v, $delimiter, $depth - 1, (string) $key);
                }
            } else {
                $temp[] = [$key => $v];
            }
        }

        // Prevent resource-greedy loop.
        // @see https://github.com/dseguy/clearPHP/blob/master/rules/no-array_merge-in-loop.md
        return array_merge(...$temp);
    }

    /**
     * keep
     *
     * @param array|object $data
     * @param array        $fields
     *
     * @return  array|object
     * @throws \InvalidArgumentException
     */
    public static function keep($data, array $fields)
    {
        if (is_array($data)) {
            return array_intersect_key($data, array_flip($fields));
        } elseif (is_object($data)) {
            $keeps = array_keys(array_diff_key(get_object_vars($data), array_flip($fields)));

            foreach ($keeps as $key) {
                if (property_exists($data, $key)) {
                    unset($data->$key);
                }
            }

            return $data;
        }

        throw new \InvalidArgumentException('Argument 1 not array or object');
    }

    /**
     * find
     *
     * @param array|object $data
     * @param callable     $callback
     * @param bool         $keepKey
     * @param int          $offset
     * @param int          $limit
     *
     * @return array
     */
    public static function find($data, callable $callback, bool $keepKey = false, int $offset = null, int $limit = null)
    {
        $results = [];
        $i = 0;
        $c = 0;

        foreach (static::toArray($data, false) as $key => $value) {
            // Set Query results
            if ($callback($value, $key)) {
                if ($offset !== null && $offset > $i) {
                    continue;
                }

                if ($limit !== null && $c >= $limit) {
                    break;
                }

                $results[$key] = $value;
                $c++;
            }

            $i++;
        }

        return $keepKey ? $results : array_values($results);
    }

    /**
     * findFirst
     *
     * @param array    $data
     * @param callable $callback
     *
     * @return  array|null
     */
    public static function findFirst($data, callable $callback)
    {
        $results = static::find($data, $callback, false, 0, 1);

        return count($results) ? $results[0] : null;
    }

}
