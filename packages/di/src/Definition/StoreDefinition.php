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
use Windwalker\DI\Exception\DefinitionException;

/**
 * The StoreDefinition class.
 */
class StoreDefinition extends DecoratorDefinition implements StoreDefinitionInterface
{
    /**
     * @var mixed
     */
    protected $cache = null;

    protected int $options;

    /**
     * StoreDefinition constructor.
     *
     * @param  DefinitionInterface  $definition
     * @param  int                  $options
     */
    public function __construct(DefinitionInterface $definition, int $options)
    {
        $this->options = $options;

        parent::__construct($definition);
    }

    public function isShared(): bool
    {
        return (bool) ($this->options & Container::SHARED);
    }

    public function isProtected(): bool
    {
        return (bool) ($this->options & Container::PROTECTED);
    }

    public function reset(): void
    {
        $this->cache = null;
    }

    /**
     * Resolve this definition.
     *
     * @param  Container  $container  The Container object.
     *
     * @return mixed
     */
    public function resolve(Container $container)
    {
        if (!$this->isShared()) {
            $this->reset();
        }

        return $this->cache ??= parent::resolve($container);
    }

    /**
     * Set new value or factory callback to this definition.
     *
     * @param  mixed  $value  Value or callable.
     *
     * @return  void
     * @throws DefinitionException
     */
    public function set($value): void
    {
        if ($this->options & Container::PROTECTED) {
            throw new DefinitionException('This value / definition is protected.');
        }

        parent::set($value);
    }

    public function extend(\Closure $closure)
    {
        $this->definition = new DecoratorDefinition(
            $this->definition,
            $closure
        );

        return $this;
    }
}
