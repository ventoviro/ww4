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
    public \ReflectionObject $reflector;

    public function __invoke(Container $container, object $instance, \ReflectionObject $reflector)
    {
        $this->instance = $instance;
        $this->reflector = $reflector;

        return $this;
    }
}
