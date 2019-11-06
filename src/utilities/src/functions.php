<?php declare(strict_types = 1);

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

namespace {

    use Windwalker\Utilities\Arr;

    if (!function_exists('show')) {
        /**
         * Dump Array or Object as tree node. If send multiple params in this method, this function will batch print it.
         *
         * @param   mixed $args Array or Object to dump.
         *
         * @since   2.0
         *
         * @return  void
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
         * @param mixed $var
         *
         * @return  bool
         *
         * @since  3.5
         */
        function is_stringable($var): bool
        {
            return (is_scalar($var) && !is_bool($var)) || (is_object($var) && method_exists($var, '__toString'));
        }
    }

    if (!function_exists('is_json')) {
        /**
         * is_json
         *
         * @param mixed $string
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
}

namespace Windwalker {

    use Windwalker\Scalars\StringObject;

    /**
     * Do some operation after value get.
     *
     * @param mixed    $value
     * @param callable $callable
     *
     * @return  mixed
     *
     * @since  3.5.1
     */
    function tap($value, callable $callable)
    {
        $result = $callable($value);

        return $result ?? $value;
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
}
