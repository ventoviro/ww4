<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later.
 */

declare(strict_types=1);

namespace Windwalker\DI\Definition;

use Windwalker\DI\Builder\ObjectBuilder;
use Windwalker\DI\Container;

/**
 * The ClassMeta class.
 *
 * @since  3.0
 */
class ObjectBuilderDefinition implements DefinitionInterface
{
    protected ObjectBuilder $builder;

    /**
     * ObjectBuilderDefinition constructor.
     *
     * @param  ObjectBuilder  $builder
     */
    public function __construct(ObjectBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Resolve this definition.
     *
     * @param  Container  $container  The Container object.
     * @param  bool       $forceNew   Refresh the cache.
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function resolve(Container $container, bool $forceNew = false)
    {
        return $container->newInstance(
            $this->builder->getClass(),
            $this->builder->getArguments()
        );
    }

    /**
     * Set new value or factory callback to this definition.
     *
     * @param  ObjectBuilder  $value  Value or callable.
     *
     * @return  void
     */
    public function set($value): void
    {
        $this->builder = $value;
    }
}
