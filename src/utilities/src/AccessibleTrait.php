<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Utilities;

use Iterator;
use Windwalker\Utilities\Contract\AccessibleInterface;
use Windwalker\Utilities\Contract\NullableInterface;

/**
 * The Accessible trait which implements AccessibleInterface.
 *
 * @see    AccessibleInterface
 * @see    NullableInterface
 *
 * @since  __DEPLOY_VERSION__
 */
trait AccessibleTrait
{
    /**
     * @var  array
     */
    protected $storage = [];

    /**
     * Get value from this object.
     *
     * @param  string  $key
     *
     * @return mixed
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
     * reset
     *
     * @param array $storage
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function reset(array $storage = [])
    {
        $this->storage = $storage;

        return $this;
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

    /**
     * Returns whether the requested key exists
     *
     * @param  mixed  $key
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Returns the value at the specified key
     *
     * @param  mixed  $key
     *
     * @return mixed
     */
    public function &offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Sets the value at the specified key to value
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        if ($key === null || $key === '') {
            $this->storage[] = $value;
            return;
        }

        $this->set($key, $value);
    }

    /**
     * Unsets the value at the specified key
     *
     * @param  mixed  $key
     *
     * @return void
     */
    public function offsetUnset($key): void
    {
        $this->remove($key);
    }

    /**
     * Dynamically retrieve the value.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function &__get($key)
    {
        return $this->get($key);
    }

    /**
     * Dynamically set the value of an attribute.
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Dynamically check if an attribute is set.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Dynamically unset an attribute.
     *
     * @param  string  $key
     *
     * @return void
     */
    public function __unset($key)
    {
        $this->remove($key);
    }

    /**
     * Get storage iterator.
     *
     * @return  Iterator
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getIterator(): Iterator
    {
        return new \ArrayIterator($this->storage);
    }

    /**
     * {@inheritDoc}
     */
    public function isNull(): bool
    {
        return $this->storage === [];
    }

    /**
     * {@inheritDoc}
     */
    public function notNull(): bool
    {
        return !$this->isNull();
    }
}
