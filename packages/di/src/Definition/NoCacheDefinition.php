<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Definition;

use Windwalker\DI\Container;

/**
 * The NoCacheDefinition class.
 */
class NoCacheDefinition extends DecoratorDefinition
{
    /**
     * Resolve this definition.
     *
     * @param  Container  $container  The Container object.
     * @param  bool       $forceNew   Refresh the cache.
     *
     * @return mixed
     */
    public function resolve(Container $container, bool $forceNew = false)
    {
        return parent::resolve($container, true);
    }
}
