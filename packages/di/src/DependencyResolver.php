<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI;

use Windwalker\DI\Builder\ObjectBuilder;
use Windwalker\DI\Exception\DependencyResolutionException;
use Windwalker\Utilities\Wrapper\RawWrapper;
use Windwalker\Utilities\Wrapper\ValueReference;

/**
 * The ObjectFactory class.
 */
class DependencyResolver
{
    public static function newInstance(Container $container, $class, array $args = [])
    {
        if ($class instanceof ObjectBuilder) {
            return $container->resolve($class->fork($args));
        }

        if (is_string($class)) {
            try {
                $reflection = new \ReflectionClass($class);
            } catch (\ReflectionException $e) {
                return false;
            }

            $constructor = $reflection->getConstructor();

            // If there are no parameters, just return a new object.
            if (null === $constructor) {
                $instance = new $class();
            } else {
                try {
                    $args = array_merge($container->whenCreating($class)->getArguments(), $args);

                    $newInstanceArgs = static::getMethodArgs($container, $constructor, $args);
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
            $instance = $class($container, $args);
        } else {
            throw new \InvalidArgumentException(
                'New instance must get first argument as class name, callable or ClassMeta object.'
            );
        }

        return $instance;
    }

    /**
     * Build an array of constructor parameters.
     *
     * @param  Container          $container
     * @param  \ReflectionMethod  $method  Method for which to build the argument array.
     * @param  array              $args    The default args if class hint not provided.
     *
     * @return array Array of arguments to pass to the method.
     *
     * @throws \ReflectionException
     * @since   2.0
     */
    protected static function getMethodArgs(Container $container, \ReflectionMethod $method, array $args = []): array
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
                        $trailing[] = static::resolveArgumentValue($container, $value);
                    }
                }

                $trailing   = array_slice($trailing, $i);
                $methodArgs = array_merge($methodArgs, $trailing);
                continue;
            }

            // Prior (2): Argument with numeric keys.
            if (array_key_exists($i, $args)) {
                $methodArgs[] = static::resolveArgumentValue($container, $args[$i]);
                continue;
            }

            // Prior (3): Argument with named keys.
            if (array_key_exists($dependencyVarName, $args)) {
                $methodArgs[] = static::resolveArgumentValue($container, $args[$dependencyVarName]);

                continue;
            }

            // // Prior (4): Argument with numeric keys.
            if (null !== $dependency) {
                $depObject           = null;
                $dependencyClassName = $dependency->getName();

                // If the dependency class name is registered with this container or a parent, use it.
                if ($container->has($dependencyClassName)) {
                    $depObject = $container->get($dependencyClassName);
                } elseif (array_key_exists($dependencyVarName, $args)) {
                    // If an arg provided, use it.
                    $methodArgs[] = static::resolveArgumentValue($container, $args[$dependencyVarName]);

                    continue;
                } elseif (!$dependency->isAbstract() && !$dependency->isInterface() && !$dependency->isTrait()) {
                    // Otherwise we create this object recursive

                    // Find child args if set
                    if (isset($args[$dependencyClassName]) && is_array($args[$dependencyClassName])) {
                        $childArgs = $args[$dependencyClassName];
                    } else {
                        $childArgs = [];
                    }

                    $depObject = $container->newInstance($dependencyClassName, $childArgs);
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
     * @param  Container  $container
     * @param  mixed      $value
     *
     * @return mixed|\Windwalker\Data\Collection
     *
     * @since  3.5.1
     */
    protected static function resolveArgumentValue(Container $container, $value)
    {
        if ($value instanceof ObjectBuilder) {
            $value = $value->setContainer($container)->newInstance();
        } elseif ($value instanceof ValueReference) {
            $v = $container->get($container->getParameters());

            if ($v === null && $container->getParent() instanceof Container) {
                $v = $container->get($container->getParent()->getParameters());
            }

            $value = $v;
        } elseif ($value instanceof RawWrapper) {
            $value = $value->get();
        }

        return $value;
    }

    /**
     * Execute a callable with dependencies.
     *
     * @param  Container    $container
     * @param  callable     $callable
     * @param  array        $args
     * @param  object|null  $context
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public static function call(Container $container, callable $callable, array $args = [], object $context = null)
    {
        $object = null;
        $method = null;

        if ($callable instanceof \Closure) {
            $ref = new \ReflectionObject($callable);

            $args = static::getMethodArgs($container, $ref->getMethod('__invoke'), $args);
        } else {
            if (is_string($callable)) {
                $callable = explode('::', $callable);
            }

            [$object, $method] = $callable;

            $ref = new \ReflectionClass($object);

            $args = static::getMethodArgs($container, $ref->getMethod($method), $args);
        }

        $callable = \Closure::fromCallable($callable);

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
