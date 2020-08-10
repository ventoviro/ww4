<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Accessible;

use Windwalker\Utilities\TypeCast;

/**
 * Trait SimpleAccessibleTrait
 */
trait SimpleAccessibleTrait
{
    /**
     * @var  array
     */
    protected $storage = [];

    /**
     * Get value from this object.
     *
     * @param  mixed  $key
     *
     * @return  mixed
     */
    public function &get($key)
    {
        $ret = null;

        if (!$this->has($key)) {
            return $ret;
        }

        $ret =& $this->storage[$key];

        return $ret;
    }

    /**
     * Set value to this object.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return  static
     */
    public function set($key, $value)
    {
        $this->storage[$key] = $value;

        return $this;
    }

    /**
     * Set value default if not exists.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function def($key, $default)
    {
        $this->storage[$key] = $this->storage[$key] ?? $default;

        return $this;
    }

    /**
     * Check a key exists or not.
     *
     * @param  mixed  $key
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function has($key): bool
    {
        return isset($this->storage[$key]);
    }

    /**
     * remove
     *
     * @param  mixed  $key
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function remove($key)
    {
        if ($this->has($key)) {
            unset($this->storage[$key]);
        }

        return $this;
    }

    /**
     * Creates a copy of storage.
     *
     * @param  bool  $recursive
     *
     * @param  bool  $onlyDumpable
     *
     * @return array
     */
    public function dump(bool $recursive = false, bool $onlyDumpable = false): array
    {
        if (!$recursive) {
            return $this->storage;
        }

        return TypeCast::toArray($this->storage, true, $onlyDumpable);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->storage;
    }

    /**
     * count
     *
     * @return  int
     *
     * @since  __DEPLOY_VERSION__
     */
    public function count(): int
    {
        return count($this->storage);
    }
}
