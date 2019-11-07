<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars\Concern;

use Windwalker\Scalars\ArrayObject;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\TypeCast;

/**
 * The ArrayModifyTrait class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait ArrayCreationTrait
{
    /**
     * combine
     *
     * @param  array|static  $values
     *
     * @return  static
     *
     * @since  3.5
     */
    public function combine($values)
    {
        return static::newInstance(array_combine($this->storage, TypeCast::toArray($values)));
    }

    /**
     * diff
     *
     * @param  array[]|static[]  ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function diff(...$args)
    {
        $args = array_map([Arr::class, 'toArray'], $args);

        return static::newInstance(array_diff($this->storage, ...$args));
    }

    /**
     * diffKeys
     *
     * @param  array[]|static[]  ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function diffKeys(...$args)
    {
        $args = array_map([TypeCast::class, 'toArray'], $args);

        return static::newInstance(array_diff_key($this->storage, ...$args));
    }

    /**
     * fill
     *
     * @param  int    $start
     * @param  int    $num
     * @param  mixed  $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function fill(int $start, int $num, $value)
    {
        return static::newInstance(array_fill($start, $num, $value));
    }

    /**
     * fillKeys
     *
     * @param  array  $keys
     * @param  mixed  $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function fillKeys(array $keys, $value)
    {
        return static::newInstance(array_fill_keys($keys, $value));
    }

    /**
     * flip
     *
     * @return  static
     *
     * @since  3.5
     */
    public function flip()
    {
        return static::newInstance(array_flip($this->storage));
    }

    /**
     * intersect
     *
     * @param  array[]|static[]  ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function intersect(...$args)
    {
        $args = array_map([TypeCast::class, 'toArray'], $args);

        return static::newInstance(array_intersect($this->storage, ...$args));
    }

    /**
     * intersectKey
     *
     * @param  array[]|static[]  ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function intersectKey(...$args)
    {
        $args = array_map([TypeCast::class, 'toArray'], $args);

        return static::newInstance(array_intersect_key($this->storage, ...$args));
    }

    /**
     * merge
     *
     * @param  array[]|static[]  ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function merge(...$args)
    {
        $args = array_map([TypeCast::class, 'toArray'], $args);

        return static::newInstance(array_merge($this->storage, ...$args));
    }

    /**
     * mergeRecursive
     *
     * @param  array[]|static[]  ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function mergeRecursive(...$args)
    {
        $args = array_map(fn ($arg) => $arg instanceof ArrayObject ? $arg->dump() : $arg, $args);

        return static::newInstance(Arr::mergeRecursive($this->storage, ...$args));
    }

    /**
     * rand
     *
     * @param  int  $num
     *
     * @return  static
     *
     * @since  3.5
     */
    public function rand(int $num = 1)
    {
        return static::newInstance(array_rand($this->storage, $num));
    }

    /**
     * range
     *
     * @param  mixed      $start
     * @param  mixed      $end
     * @param  int|float  $step
     *
     * @return  static
     *
     * @since  3.5
     */
    public static function range($start, $end, $step = 1)
    {
        return static::newInstance(range($start, $end, $step));
    }
}
