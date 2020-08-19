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
use Windwalker\DI\Exception\DependencyResolutionException;

/**
 * The ObjectBuilder class.
 */
class ObjectBuilderDefinition implements DefinitionInterface
{
    /**
     * Property class.
     *
     * @var  string
     */
    protected string $class;

    /**
     * Property arguments.
     *
     * @var  array
     */
    protected array $arguments = [];

    /**
     * Property caches.
     *
     * @var  array
     */
    protected array $caches = [];

    /**
     * Property container.
     *
     * @var  ?Container
     */
    protected ?Container $container = null;

    /**
     * @var callable[]
     */
    protected array $extends = [];

    protected ?object $instance = null;

    public function fork(?array $args = null): static
    {
        $new = clone $this;

        if ($args !== null) {
            $new->setArguments($args);
        }

        return $new;
    }

    /**
     * ClassMeta constructor.
     *
     * @param string|callable $class
     * @param Container       $container
     */
    public function __construct($class, ?Container $container = null)
    {
        $this->container = $container;
        $this->class = $class;
    }

    /**
     * Resolve this definition.
     *
     * @param  Container  $container  The Container object.
     *
     * @return object
     * @throws \ReflectionException
     */
    public function resolve(Container $container)
    {
        return $container->newInstance(
            $this->getClass(),
            $this->getArguments()
        );
    }

    /**
     * Set new value or factory callback to this definition.
     *
     * @param  string  $value  Value or callable.
     *
     * @return  void
     */
    public function set($value): void
    {
        $this->class = $value;
    }

    /**
     * Method to get property Argument
     *
     * @param  string $name
     * @param  mixed  $default
     *
     * @return array
     * @throws DependencyResolutionException
     * @throws \ReflectionException
     */
    public function getArgument($name, $default = null)
    {
        if (!isset($this->arguments[$name])) {
            return $default;
        }

        return $this->caches[$name] ??= $this->container->call($this->arguments[$name]);
    }

    /**
     * Method to set property argument
     *
     * @param   string $name
     * @param   mixed  $value
     *
     * @return  static Return self to support chaining.
     */
    public function setArgument($name, $value)
    {
        if (!$value instanceof \Closure) {
            $value = fn() => $value;
        }

        $this->arguments[$name] = $value;
        unset($this->caches[$name]);

        return $this;
    }

    /**
     * hasArgument
     *
     * @param string $name
     *
     * @return  bool
     *
     * @since  3.5.1
     */
    public function hasArgument(string $name): bool
    {
        return isset($this->arguments[$name]);
    }

    /**
     * removeArgument
     *
     * @param   string $name
     *
     * @return  static
     */
    public function removeArgument($name)
    {
        unset($this->arguments[$name], $this->caches[$name]);

        return $this;
    }

    /**
     * Method to get property Arguments
     *
     * @return  array
     * @throws DependencyResolutionException
     * @throws \ReflectionException
     */
    public function getArguments()
    {
        $args = [];

        foreach ($this->arguments as $name => $callable) {
            $args[$name] = $this->getArgument($name);
        }

        return $args;
    }

    /**
     * Method to set property arguments
     *
     * @param   array $arguments
     *
     * @return  static  Return self to support chaining.
     */
    public function setArguments($arguments)
    {
        foreach ($arguments as $name => $argument) {
            $this->setArgument($name, $argument);
        }

        return $this;
    }

    /**
     * reset
     *
     * @return  static
     */
    public function reset()
    {
        $this->arguments = [];
        $this->caches = [];
        $this->extends = [];

        return $this;
    }

    /**
     * extend
     *
     * @param callable $handler
     *
     * @return  $this
     *
     * @since  3.5.20
     */
    public function extend(callable $handler)
    {
        $this->extends[] = $handler;

        return $this;
    }

    /**
     * clearExtends
     *
     * @return  $this
     *
     * @since  3.5.20
     */
    public function clearExtends()
    {
        $this->extends = [];

        return $this;
    }

    /**
     * __call
     *
     * @param   string $name
     * @param   array  $args
     *
     * @return  mixed
     * @throws DependencyResolutionException
     * @throws \ReflectionException
     */
    public function __call($name, $args)
    {
        $allowMethods = [
            'bind',
            'bindShared',
        ];

        if (in_array($name, $allowMethods, true)) {
            return $this->container->$name($this->class, ...$args);
        }

        $allowMethods = [
            'newInstance',
            'createObject',
            'createSharedObject',
        ];

        if (in_array($name, $allowMethods, true)) {
            $arguments = array_merge($this->getArguments(), $args[0] ?? []);

            $object = $this->container->$name($this->class, $arguments);

            foreach ($this->extends as $extend) {
                $object = $extend($this->container, $object);
            }

            return $object;
        }

        throw new \BadMethodCallException(__METHOD__ . '::' . $name . '() not found.');
    }

    /**
     * Method to get property Container
     *
     * @return  Container
     *
     * @since  3.5.1
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Method to set property container
     *
     * @param   Container $container
     *
     * @return  static  Return self to support chaining.
     *
     * @since  3.5.1
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Method to get property Class
     *
     * @return  string
     *
     * @since  3.5.19
     */
    public function getClass()
    {
        return $this->class;
    }
}
