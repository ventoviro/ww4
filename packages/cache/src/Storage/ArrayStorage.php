<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\Cache\Storage;

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
    public function get(string $key)
    {
        $data = $this->storage[$key] ?? null;

        if ($data === null) {
            return null;
        }

        [$expiration, $value] = $data;

        if (time() > $expiration) {
            return null;
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        if (!isset($this->storage[$key])) {
            return false;
        }

        [$expiration, $value] = $this->storage[$key];

        return time() <= $expiration;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->storage = [];

        return true;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): bool
    {
        unset($this->storage[$key]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, int $expiration = 0): bool
    {
        $this->storage[$key] = [
            $expiration,
            $value,
        ];

        return true;
    }
}
