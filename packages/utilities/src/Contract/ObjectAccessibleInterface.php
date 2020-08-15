<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Contract;

/**
 * Interface ObjectAccessibleInterface
 */
interface ObjectAccessibleInterface
{
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
