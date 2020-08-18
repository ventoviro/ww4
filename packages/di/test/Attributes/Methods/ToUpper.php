<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Test\Attributes\Methods;

use Windwalker\DI\Attributes\MethodAttributeInterface;
use Windwalker\DI\Container;

/**
 * The ToUpper class.
 */
class ToUpper implements MethodAttributeInterface
{
    /**
     * __invoke
     *
     * @param  Container          $container
     * @param  \Closure           $instance
     * @param  \ReflectionMethod  $property
     *
     * @return  object
     */
    public function __invoke(Container $container, \Closure $instance, \ReflectionMethod $property): object
    {
        return fn () => strtoupper($instance());
    }
}
