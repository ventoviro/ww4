<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Storage;

/**
 * The NullStorage class.
 */
class NullStorage implements StorageInterface
{
    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, int $expiration = 0): bool
    {
        return true;
    }
}
