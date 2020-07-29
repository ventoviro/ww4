<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Traits;

use Windwalker\Cache\CachePool;
use Windwalker\Cache\Serializer\RawSerializer;
use Windwalker\Cache\Storage\ArrayStorage;

/**
 * The RuntimeCacheTrait class.
 */
trait RuntimeCacheTrait
{
    protected static ?CachePool $cache = null;

    public static function getCacheId(?string $id = null): string
    {
        return sha1((string) $id);
    }

    public static function getCacheInstance(): CachePool
    {
        if (static::$cache === null) {
            static::cacheReset();
        }

        return static::$cache;
    }

    protected static function cacheGet(?string $id = null)
    {
        return static::getCacheInstance()->get(static::getCacheId($id));
    }

    protected static function cacheSet(?string $id = null, $value = null)
    {
        static::getCacheInstance()->set(static::getCacheId($id), $value);

        return $value;
    }

    protected static function cacheHas(?string $id = null): bool
    {
        return static::getCacheInstance()->has(static::getCacheId($id));
    }

    public static function cacheReset(): void
    {
        static::$cache = new CachePool(new ArrayStorage(), new RawSerializer());
    }

    protected static function once(?string $id, callable $closure, bool $refresh = false)
    {
        $key = static::getCacheId($id);
        $cache = static::getCacheInstance();

        if ($refresh) {
            $cache->delete($key);
        }

        return $cache->call($key, $closure);
    }
}
