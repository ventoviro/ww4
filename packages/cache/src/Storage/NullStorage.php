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
    public function get(string $key, array $options = [])
    {
        //
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
    public function clear(): void
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): void
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, array $options = []): void
    {
        //
    }
}
