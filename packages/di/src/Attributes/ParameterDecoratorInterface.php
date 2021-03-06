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
 * Interface ParameterAttributeInterface
 */
interface ParameterDecoratorInterface
{
    /**
     * handle
     *
     * @param  Container             $container
     * @param                        $value
     * @param  \ReflectionParameter  $reflector
     *
     * @return
     */
    public function __invoke(Container $container, $value, \ReflectionParameter $reflector);
}
