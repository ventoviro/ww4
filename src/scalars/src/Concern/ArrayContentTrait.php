<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars\Concern;

use Windwalker\Utilities\Arr;
use Windwalker\Utilities\TypeCast;

/**
 * The ArrayContentTrait class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait ArrayContentTrait
{
    /**
     * first
     *
     * @param callable $conditions
     *
     * @return  mixed
     */
    public function first(callable $conditions = null)
    {
        if ($conditions) {
            foreach ($this->storage as $key => $value) {
                if ($conditions($value, $key)) {
                    return $value;
                }
            }

            return null;
        }

        return $array[array_key_first($this->storage)] ?? null;
    }

    /**
     * last
     *
     * @param callable $conditions
     *
     * @return  mixed
     */
    public function last(callable $conditions = null)
    {
        $array = TypeCast::toArray($this);

        if ($conditions) {
            $prev = null;

            foreach ($array as $key => $value) {
                if ($conditions($value, $key)) {
                    $prev = $value;
                }
            }

            return $prev;
        }

        return $array[array_key_last($array)] ?? null;
    }

    /**
     * flatten
     *
     * @param string $delimiter
     * @param int    $depth
     * @param string $prefix
     *
     * @return  static
     *
     * @since  3.5.10
     */
    public function flatten(string $delimiter = '.', int $depth = 0, ?string $prefix = null)
    {
        return static::newInstance(Arr::flatten($this->dump(), $delimiter, $depth, $prefix));
    }

    /**
     * collapse
     *
     * @param int $depth
     *
     * @return  static
     *
     * @since  3.5.10
     */
    public function collapse(int $depth = 0)
    {
        return $this->flatten('.', $depth)->values();
    }
}
