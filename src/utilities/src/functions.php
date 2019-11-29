<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace {

    use Windwalker\Utilities\Arr;

    if (!function_exists('show')) {
        /**
         * Dump Array or Object as tree node. If send multiple params in this method, this function will batch print it.
         *
         * @param  mixed  $args  Array or Object to dump.
         *
         * @return  void
         * @since   2.0
         *
         */
        function show(...$args)
        {
            Arr::show(...$args);
        }
    }

    if (!function_exists('is_stringable')) {
        /**
         * is_stringable
         *
         * @param  mixed  $var
         *
         * @return  bool
         *
         * @since  3.5
         */
        function is_stringable($var): bool
        {
            if (is_array($var)) {
                return false;
            }

            if (is_object($var) && !method_exists($var, '__toString')) {
                return false;
            }

            if (is_resource($var)) {
                return false;
            }

            return true;
        }
    }

    if (!function_exists('is_json')) {
        /**
         * is_json
         *
         * @param  mixed  $string
         *
         * @return  bool
         *
         * @since  3.5.8
         */
        function is_json($string): bool
        {
            if (!is_string($string)) {
                return false;
            }

            json_decode($string);

            return json_last_error() === JSON_ERROR_NONE;
        }
    }

    include_once __DIR__ . '/serializer.php';
}

declare(strict_types=1);

namespace Windwalker {

    use Closure;
    use Traversable;
    use Windwalker\Utilities\Compare\WhereWrapper;
    use Windwalker\Utilities\Wrapper\ValueReference;

    /**
     * Do some operation after value get.
     *
     * @param  mixed     $value
     * @param  callable  $callable
     *
     * @return  mixed
     *
     * @since  3.5.1
     */
    function tap($value, callable $callable)
    {
        $callable($value);

        return $value;
    }

    /**
     * Count NULL as 0 to workaround some code before php7.2
     *
     * @param  mixed  $value
     * @param  int    $mode
     *
     * @return  int
     *
     * @since  3.5.13
     */
    function count($value, $mode = COUNT_NORMAL): int
    {
        return $value !== null ? \count($value, $mode) : 0;
    }

    /**
     * fread_all
     *
     * @param  resource  $fd
     * @param  int       $length
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    function fread_all($fd, int $length = 1024): string
    {
        $data = '';

        while (!feof($fd)) {
            $data .= fread($fd, $length);
        }

        return $data;
    }

    /**
     * iterator_keys
     *
     * @param  Traversable  $iterable
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    function iterator_keys(Traversable $iterable): array
    {
        return array_keys(iterator_to_array($iterable));
    }

    /**
     * where
     *
     * @param  mixed   $var1
     * @param  string  $operator
     * @param  mixed   $var2
     * @param  bool    $strict
     *
     * @return  WhereWrapper
     *
     * @since  __DEPLOY_VERSION__
     */
    function where($var1, string $operator, $var2, bool $strict = false): WhereWrapper
    {
        return new WhereWrapper($var1, $operator, $var2, $strict);
    }

    /**
     * value
     *
     * @param  mixed|Closure  $value
     * @param  mixed          ...$args
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }

    /**
     * ref
     *
     * @param  string       $path
     * @param  string|null  $delimiter
     *
     * @return  ValueReference
     *
     * @since  __DEPLOY_VERSION__
     */
    function ref(string $path, ?string $delimiter = null): ValueReference
    {
        return new ValueReference($path, $delimiter);
    }
}
