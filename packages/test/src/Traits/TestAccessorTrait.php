<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Test\Traits;

use Windwalker\Utilities\Reflection\ReflectAccessor;

/**
 * The TestAccessorTrait class.
 */
trait TestAccessorTrait
{
    /**
     * getValue
     *
     * @param  mixed   $obj
     * @param  string  $name
     *
     * @return  mixed
     *
     * @throws \ReflectionException
     */
    public function getValue($obj, string $name)
    {
        return ReflectAccessor::getValue($obj, $name);
    }

    /**
     * setValue
     *
     * @param  object  $obj
     * @param  string  $name
     * @param  mixed   $value
     *
     * @return  void
     *
     * @throws \ReflectionException
     */
    public function setValue(object $obj, string $name, $value): void
    {
        ReflectAccessor::setValue($obj, $name, $value);
    }

    /**
     * invoke
     *
     * @param  object  $obj
     * @param  string  $method
     * @param  mixed   ...$args
     *
     * @return  mixed
     *
     * @throws \ReflectionException
     */
    public function invoke(object $obj, string $method, ...$args)
    {
        return ReflectAccessor::invoke($obj, $method, ...$args);
    }
}
