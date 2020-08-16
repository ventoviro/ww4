<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionObject;
use Windwalker\Data\Collection;
use Windwalker\DI\Builder\ObjectBuilder;
use Windwalker\DI\Definition\DefinitionInterface;
use Windwalker\DI\Definition\ObjectBuilderDefinition;
use Windwalker\DI\Exception\DependencyResolutionException;
use Windwalker\Utilities\Wrapper\RawWrapper;
use Windwalker\Utilities\Wrapper\ValueReference;

/**
 * The ObjectFactory class.
 */
class DependencyResolver
{
    protected Container $container;

    /**
     * DependencyResolver constructor.
     *
     * @param  Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function newInstance($class, array $args = [])
    {
        if ($class instanceof ObjectBuilder) {
            $class = new ObjectBuilderDefinition($class->fork($args));
        }

        if ($class instanceof DefinitionInterface) {
            return $this->container->resolve($class);
        }

        if (is_string($class)) {
            try {
                $reflection = new ReflectionClass($class);
            } catch (ReflectionException $e) {
                return false;
            }

            $constructor = $reflection->getConstructor();

            // If there are no parameters, just return a new object.
            if (null === $constructor) {
                $instance = new $class();
            } else {
                try {
                    $args = array_merge($this->container->whenCreating($class)->getArguments(), $args);

                    $newInstanceArgs = $this->getMethodArgs($constructor, $args);
                } catch (DependencyResolutionException $e) {
                    throw new DependencyResolutionException(
                        $e->getMessage() . ' / Target class: ' . $class,
                        $e->getCode(),
                        $e
                    );
                }

                // Create a callable for the dataStore
                $instance = $reflection->newInstanceArgs($newInstanceArgs);
            }
        } elseif (is_callable($class)) {
            $instance = $class($this->container, $args);
        } else {
            throw new InvalidArgumentException(
                'New instance must get first argument as class name, callable or ClassMeta object.'
            );
        }

        return $instance;
    }

    /**
     * Build an array of constructor parameters.
     *
     * @param  ReflectionMethod  $method  Method for which to build the argument array.
     * @param  array              $args    The default args if class hint not provided.
     *
     * @return array Array of arguments to pass to the method.
     *
     * @throws ReflectionException
     * @since   2.0
     */
    protected function getMethodArgs(ReflectionMethod $method, array $args = []): array
    {
        $methodArgs = [];

        foreach ($method->getParameters() as $i => $param) {
            $dependency        = $param->getClass();
            $dependencyVarName = $param->getName();

            // Prior (1): Handler ...$args
            if ($param->isVariadic()) {
                $trailing = [];

                foreach ($args as $key => $value) {
                    if (is_numeric($key)) {
                        $trailing[] = $this->resolveArgumentValue($value);
                    }
                }

                $trailing   = array_slice($trailing, $i);
                $methodArgs = array_merge($methodArgs, $trailing);
                continue;
            }

            // Prior (2): Argument with numeric keys.
            if (array_key_exists($i, $args)) {
                $methodArgs[] = $this->resolveArgumentValue($args[$i]);
                continue;
            }

            // Prior (3): Argument with named keys.
            if (array_key_exists($dependencyVarName, $args)) {
                $methodArgs[] = $this->resolveArgumentValue($args[$dependencyVarName]);

                continue;
            }

            // // Prior (4): Argument with numeric keys.
            if (null !== $dependency) {
                $depObject           = null;
                $dependencyClassName = $dependency->getName();

                // If the dependency class name is registered with this container or a parent, use it.
                if ($this->container->has($dependencyClassName)) {
                    $depObject = $this->container->get($dependencyClassName);
                } elseif (array_key_exists($dependencyVarName, $args)) {
                    // If an arg provided, use it.
                    $methodArgs[] = $this->resolveArgumentValue($args[$dependencyVarName]);

                    continue;
                } elseif (!$dependency->isAbstract() && !$dependency->isInterface() && !$dependency->isTrait()) {
                    // Otherwise we create this object recursive

                    // Find child args if set
                    if (isset($args[$dependencyClassName]) && is_array($args[$dependencyClassName])) {
                        $childArgs = $args[$dependencyClassName];
                    } else {
                        $childArgs = [];
                    }

                    $depObject = $this->container->newInstance($dependencyClassName, $childArgs);
                }

                if ($depObject instanceof $dependencyClassName) {
                    $methodArgs[] = $depObject;

                    continue;
                }
            }

            if ($param->isOptional()) {
                // Finally, if there is a default parameter, use it.
                if ($param->isDefaultValueAvailable()) {
                    $methodArgs[] = $param->getDefaultValue();
                }

                continue;
            }

            // Couldn't resolve dependency, and no default was provided.
            throw new DependencyResolutionException(sprintf('Could not resolve dependency: $%s', $dependencyVarName));
        }

        return $methodArgs;
    }

    /**
     * resolveArgumentValue
     *
     * @param  mixed      $value
     *
     * @return mixed|Collection
     *
     * @since  3.5.1
     */
    protected function resolveArgumentValue($value)
    {
        if ($value instanceof ObjectBuilder) {
            $value = $this->container->resolve($value);
        } elseif ($value instanceof ValueReference) {
            $v = $value($this->container->getParameters());

            if ($v === null && $this->container->getParent() instanceof Container) {
                $v = $value($this->container->getParent()->getParameters());
            }

            $value = $v;
        } elseif ($value instanceof RawWrapper) {
            $value = $value();
        }

        return $value;
    }

    /**
     * Execute a callable with dependencies.
     *
     * @param  callable     $callable
     * @param  array        $args
     * @param  object|null  $context
     *
     * @return mixed
     *
     * @throws ReflectionException
     */
    public function call(callable $callable, array $args = [], object $context = null)
    {
        $object = null;
        $method = null;

        if ($callable instanceof Closure) {
            $ref = new ReflectionObject($callable);

            $args = $this->getMethodArgs($ref->getMethod('__invoke'), $args);
        } else {
            if (is_string($callable)) {
                $callable = explode('::', $callable);
            }

            [$object, $method] = $callable;

            $ref = new ReflectionClass($object);

            $args = $this->getMethodArgs($ref->getMethod($method), $args);
        }

        $callable = Closure::fromCallable($callable);

        if ($callable) {
            $callable = $callable->bindTo($context, $context);
        }

        $closure = static function () use ($args, $callable) {
            switch (count($args)) {
                case 0:
                    return $callable();
                case 1:
                    return $callable($args[0]);
                case 2:
                    return $callable($args[0], $args[1]);
                case 3:
                    return $callable($args[0], $args[1], $args[2]);
                case 4:
                    return $callable($args[0], $args[1], $args[2], $args[3]);
                default:
                    return call_user_func_array($callable, $args);
            }
        };

        return $closure();
    }
}
