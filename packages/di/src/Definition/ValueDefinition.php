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
 * The ValueDefinition class.
 */
class ValueDefinition implements DefinitionInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * ValueDefinition constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->set($value);
    }

    /**
     * resolve
     *
     * @param  Container  $container
     * @param  bool       $forceNew
     *
     * @return  mixed
     */
    public function resolve(Container $container, bool $forceNew = false)
    {
        return $this->value;
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
        $this->value = $value;
    }
}
