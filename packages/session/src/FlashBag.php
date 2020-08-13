<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session;

/**
 * The FlashBag class.
 */
class FlashBag
{
    protected ?array $storage;

    protected string $key;

    /**
     * FlashBag constructor.
     *
     * @param  array|null  $storage
     * @param  string      $key
     */
    public function __construct(?array &$storage)
    {
        $this->storage = &$storage;
    }

    public function add($value, string $type = 'info'): void
    {
        $this->storage[$type] ??= [];

        $this->storage[$type][] = $value;
    }

    public function peek(?string $type = null): ?array
    {
        if ($type) {
            return $this->storage[$type] ?? null;
        }

        return $this->storage;
    }

    public function get(string $type)
    {
        $msg = $this->storage[$type] ?? null;

        unset($this->storage[$type]);

        return $msg;
    }

    public function all()
    {
        $storage = $this->storage[$this->key];

        $this->storage = [];

        return $storage;
    }

    /**
     * @return array|null
     */
    public function &getStorage(): ?array
    {
        return $this->storage;
    }

    /**
     * @param  array|null  $storage
     *
     * @return  static  Return self to support chaining.
     */
    public function setStorage(?array &$storage)
    {
        $this->storage = &$storage;

        return $this;
    }
}
