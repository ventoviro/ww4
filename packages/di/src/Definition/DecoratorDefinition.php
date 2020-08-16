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
 * The DecoratorDefinition class.
 */
class DecoratorDefinition implements DefinitionInterface
{
    protected DefinitionInterface $definition;

    protected ?\Closure $handler = null;

    /**
     * DecoratorDefinition constructor.
     *
     * @param  DefinitionInterface  $definition
     * @param  \Closure|null        $handler
     */
    public function __construct(DefinitionInterface $definition, ?\Closure $handler = null)
    {
        $this->definition = $definition;
        $this->handler = $handler;
    }

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
        $handler = $this->handler ?? fn ($container, $value) => $value;

        return $handler($container, $this->definition->resolve($container, $forceNew));
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
        $this->definition->set($value);
    }
}
