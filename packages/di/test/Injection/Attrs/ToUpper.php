<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Test\Injection\Attrs;

use Attribute;
use Windwalker\DI\Attributes\MethodDecoratorInterface;
use Windwalker\DI\Container;

/**
 * The ToUpper class.
 */
@@Attribute
class ToUpper implements MethodDecoratorInterface
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
        return fn (...$args) => strtoupper($instance(...$args));
    }
}
