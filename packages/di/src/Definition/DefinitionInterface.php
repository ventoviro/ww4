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
 * Interface DefinitionInterface
 */
interface DefinitionInterface
{
    /**
     * Resolve this definition.
     *
     * @param  Container  $container The Container object.
     * @param  bool       $forceNew  Refresh the cache.
     *
     * @return mixed
     */
    public function resolve(Container $container, bool $forceNew = false);

    /**
     * Set new value or factory callback to this definition.
     *
     * @param mixed $value  Value or callable.
     *
     * @return  void
     */
    public function set($value): void;
}
