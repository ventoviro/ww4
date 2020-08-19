<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Test\Injection\Attrs;

use Windwalker\DI\Attributes\ObjectDecoratorAttributeInterface;
use Windwalker\DI\Container;

/**
 * The Wrapped class.
 */
@@\Attribute(\Attribute::TARGET_CLASS)
class Wrapped implements ObjectDecoratorAttributeInterface
{
    public object $instance;
    public \ReflectionClass $reflector;

    public function __invoke(Container $container, \Closure $builder, array $args, \ReflectionClass $reflector)
    {
        $this->instance = $builder;
        $this->reflector = $reflector;

        return function () use ($container, $args, $builder) {
            $this->instance = $builder($container, $args);
            return $this;
        };
    }
}
