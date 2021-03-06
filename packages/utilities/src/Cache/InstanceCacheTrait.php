<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Cache;

/**
 * Trait InstanceCacheTrait
 */
trait InstanceCacheTrait
{
    protected array $cacheStorage = [];

    protected function cacheGet(string $id = null)
    {
        return $this->cacheStorage[$id] ?? null;
    }

    protected function cacheSet(string $id = null, $value = null)
    {
        $this->cacheStorage[$id] = $value;

        return $value;
    }

    protected function cacheHas(string $id = null): bool
    {
        return isset($this->cacheStorage[$id]);
    }

    public function cacheRemove(string $id): static
    {
        unset($this->cacheStorage[$id]);

        return $this;
    }

    protected function cacheReset(): static
    {
        $this->cacheStorage = [];

        return $this;
    }

    protected function once(string $id, callable $closure, bool $refresh = false)
    {
        if ($refresh) {
            unset($this->cacheStorage[$id]);
        }

        return $this->cacheStorage[$id] ??= $closure();
    }
}
