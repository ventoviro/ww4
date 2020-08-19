<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Test\Injection\Attrs;

use Windwalker\DI\Attributes\ParameterDecoratorInterface;
use Windwalker\DI\Container;
use Windwalker\Scalars\StringObject;

/**
 * The ParamLower class.
 */
@@\Attribute(\Attribute::TARGET_PARAMETER)
class ParamLower implements ParameterDecoratorInterface
{
    /**
     * handle
     *
     * @param  Container             $container
     * @param                        $value
     * @param  \ReflectionParameter  $reflector
     *
     * @return string
     */
    public function __invoke(Container $container, $value, \ReflectionParameter $reflector)
    {
        /** @var StringObject $value */
        return $value->toLowerCase();
    }
}
