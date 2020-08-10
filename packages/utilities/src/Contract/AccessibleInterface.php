<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Contract;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * The AccessibleInterface class.
 *
 * @since  __DEPLOY_VERSION__
 */
interface AccessibleInterface extends
    SimpleAccessibleInterface,
    JsonSerializable,
    ArrayAccess,
    DumpableInterface,
    Countable,
    IteratorAggregate,
    NullableInterface
{
    /**
     * Returns whether the requested key exists
     *
     * @param  mixed  $key
     *
     * @return bool
     */
    public function offsetExists($key): bool;

    /**
     * Returns the value at the specified key
     *
     * @param  mixed  $key
     *
     * @return mixed
     */
    public function &offsetGet($key);

    /**
     * Sets the value at the specified key to value
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value): void;

    /**
     * Unsets the value at the specified key
     *
     * @param  mixed  $key
     *
     * @return void
     */
    public function offsetUnset($key): void;

    /**
     * Dynamically retrieve the value.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function &__get($key);

    /**
     * Dynamically set the value of an attribute.
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return void
     */
    public function __set($key, $value);

    /**
     * Dynamically check if an attribute is set.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function __isset($key);

    /**
     * Dynamically unset an attribute.
     *
     * @param  string  $key
     *
     * @return void
     */
    public function __unset($key);
}
