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
 * The Decorator class.
 */
@@\Attribute(\Attribute::TARGET_CLASS)
class Decorator implements ObjectDecoratorInterface
{
    protected string $class;

    protected array $args;

    /**
     * Decorator constructor.
     *
     * @param  string  $class
     * @param  mixed   ...$args
     */
    public function __construct(string $class, ...$args)
    {
        $this->class = $class;
        $this->args = $args;
    }

    /**
     * __invoke
     *
     * @param  Container         $container
     * @param  \Closure          $builder
     * @param  \ReflectionClass  $reflector
     *
     * @return  mixed
     */
    public function __invoke(Container $container, \Closure $builder, \ReflectionClass $reflector)
    {
        return fn (...$args) => $container->newInstance($this->class, [$builder(...$args), ...$this->args]);
    }
}
