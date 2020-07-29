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
 * Trait InstanceCacheTrait
 */
trait InstanceCacheTrait
{
    protected ?CachePool $cache = null;

    public function getCacheId(?string $id = null): string
    {
        return sha1((string) $id);
    }

    public function getCacheInstance(): CachePool
    {
        if ($this->cache === null) {
            $this->cacheReset();
        }

        return $this->cache;
    }

    protected function cacheGet(?string $id = null)
    {
        return $this->getCacheInstance()->get($this->getCacheId($id));
    }

    protected function cacheSet(?string $id = null, $value = null)
    {
        $this->getCacheInstance()->set($this->getCacheId($id), $value);

        return $value;
    }

    protected function cacheHas(?string $id = null): bool
    {
        return $this->getCacheInstance()->has($this->getCacheId($id));
    }

    public function cacheReset(): static
    {
        $this->cache = new CachePool(new ArrayStorage(), new RawSerializer());

        return $this;
    }

    protected function once(?string $id, callable $closure, bool $refresh = false)
    {
        $key = $this->getCacheId($id);
        $cache = $this->getCacheInstance();

        if ($refresh) {
            $cache->delete($key);
        }

        return $cache->call($key, $closure);
    }
}
