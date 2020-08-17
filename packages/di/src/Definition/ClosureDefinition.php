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
 * The CallbackDefinition class.
 */
class ClosureDefinition implements DefinitionInterface
{
    protected \Closure $handler;

    /**
     * CallbackDefinition constructor.
     *
     * @param  \Closure  $handler
     */
    public function __construct(\Closure $handler)
    {
        $this->handler = $handler;
    }

    /**
     * resolve
     *
     * @param  Container  $container
     *
     * @return mixed
     */
    public function resolve(Container $container)
    {
        return ($this->handler)($container);
    }

    /**
     * Set new value or factory callback to this definition.
     *
     * @param  mixed  $value  Value or callable.
     *
     * @return  void
     */
    public function set($value): void
    {
        $this->handler = $value;
    }
}
