<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars\Concern;

use Windwalker\Scalars\ArrayObject;
use Windwalker\Scalars\ScalarsFactory;
use Windwalker\Scalars\StringObject;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\TypeCast;

/**
 * The ArrayLoopConcern class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait ArrayLoopTrait
{
    /**
     * reduce
     *
     * @param callable $callable
     * @param mixed    $initial
     *
     * @return  ArrayObject|StringObject|mixed
     *
     * @since  3.5
     */
    public function reduce(callable $callable, $initial = null)
    {
        $result = array_reduce($this->storage, $callable, $initial);

        return ScalarsFactory::fromNative($result);
    }

    /**
     * walk
     *
     * @param callable $callable
     * @param mixed    $userdata
     *
     * @return  static
     *
     * @since  3.5
     */
    public function walk(callable $callable, $userdata = null)
    {
        $new = static::newInstance($this->storage);

        array_walk($new->storage, $callable, $userdata);

        return $new;
    }

    /**
     * walkRecursive
     *
     * @param callable $callable
     * @param mixed    $userdata
     *
     * @return  static
     *
     * @since  3.5
     */
    public function walkRecursive(callable $callable, $userdata = null)
    {
        $new = static::newInstance($this->storage);

        array_walk_recursive($new->storage, $callable, $userdata);

        return $new;
    }

    /**
     * each
     *
     * @param callable $callback
     *
     * @return  static
     */
    public function each(callable $callback)
    {
        foreach ($this as $key => $value) {
            $return = $callback($value, $key);

            if ($return === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * find
     *
     * @param callable $callback
     * @param bool     $keepKey
     * @param int      $offset
     * @param int      $limit
     *
     * @return static
     */
    public function find(callable $callback, $keepKey = false, $offset = null, $limit = null)
    {
        return static::newInstance(Arr::find($this->storage, $callback, $keepKey, $offset, $limit));
    }

    /**
     * query
     *
     * @param array|callable $queries
     * @param bool           $strict
     * @param bool           $keepKey
     *
     * @return  static
     *
     * @since  3.5.8
     */
    public function query($queries = [], bool $strict = false, bool $keepKey = false)
    {
        return static::newInstance(Arr::query($this->storage, $queries, $strict, $keepKey));
    }

    /**
     * filter
     *
     * @param callable $callback
     *
     * @return  static
     */
    public function filter(callable $callback = null)
    {
        return $this->find($callback, true);
    }

    /**
     * findFirst
     *
     * @param callable $callback
     *
     * @return  mixed
     */
    public function findFirst(callable $callback = null)
    {
        return Arr::findFirst($this->storage, $callback);
    }

    /**
     * reject
     *
     * @param callable $callback
     * @param bool     $keepKey
     *
     * @return  static
     */
    public function reject(callable $callback, $keepKey = false)
    {
        return static::newInstance(Arr::reject($this->storage, $callback, $keepKey));
    }

    /**
     * partition
     *
     * @param callable $callback
     * @param bool     $keepKey
     *
     * @return  static[]
     */
    public function partition(callable $callback, $keepKey = false)
    {
        $true  = [];
        $false = [];

        if (is_string($callback)) {
            $callback = static function ($value) use ($callback) {
                return $callback($value);
            };
        }

        foreach ($this->storage as $key => $value) {
            if ($callback($value, $key)) {
                $true[$key] = $value;
            } else {
                $false[$key] = $value;
            }
        }

        if (!$keepKey) {
            $true  = array_values($true);
            $false = array_values($false);
        }

        return [
            static::newInstance($true),
            static::newInstance($false),
        ];
    }

    /**
     * Mapping all elements.
     *
     * @param callable $callback
     * @param array    ...$args
     *
     * @return  static  Support chaining.
     *
     * @since   2.0.9
     */
    public function map($callback, ...$args)
    {
        // Keep keys same as origin
        return static::newInstance(array_map($callback, $this->storage, ...$args));
    }

    /**
     * mapRecursive
     *
     * @param callable $callback
     * @param bool     $useKeys
     *
     * @return  static
     *
     * @since  3.5.8
     */
    public function mapRecursive(callable $callback, $useKeys = false)
    {
        return $this->map(static function ($value) use ($callback, $useKeys) {
            if (is_array($value) || is_object($value)) {
                return (static::newInstance($value))->map($callback, $useKeys);
            }

            return $callback($value);
        }, $useKeys);
    }

    /**
     * mapWithKeys
     *
     * @param callable $handler
     *
     * @return  static
     *
     * @since  3.5.12
     */
    public function mapWithKeys(callable $handler)
    {
        return static::newInstance(Arr::mapWithKeys($this->storage, $handler));
    }

    /**
     * mapAs
     *
     * @param  string  $class
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function mapAs(string $class)
    {
        return static::newInstance(TypeCast::mapAs($this->storage, $class));
    }
}
