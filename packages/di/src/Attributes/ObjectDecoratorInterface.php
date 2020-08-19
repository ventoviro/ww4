<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Attributes;

use Windwalker\DI\Container;

/**
 * Interface ClassDecoratorAttributeInterface
 */
interface ObjectDecoratorInterface
{
    public function __invoke(Container $container, \Closure $builder, \ReflectionClass $reflector);
}
