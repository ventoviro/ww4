<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Cache\Pool;

use Windwalker\Cache\Storage\StorageInterface;

/**
 * Runtime Storage.
 *
 * @since 2.0
 */
class ArrayStorage implements StorageInterface
{
    /**
     * Property storage.
     *
     * @var  array
     */
    protected $storage = [];

    /**
     * @inheritDoc
     */
    public function get(string $key, array $options = [])
    {
        return $this->storage[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return isset($this->storage[$key]);
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->storage = [];
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): void
    {
        unset($this->storage[$key]);
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, array $options = []): void
    {
        $this->storage[$key] = $value;
    }
}
