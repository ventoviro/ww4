<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use Windwalker\DI\Definition\DefinitionInterface;
use Windwalker\DI\Definition\ObjectBuilderDefinition;
use Windwalker\DI\Exception\DefinitionResolveException;
use Windwalker\DI\Exception\DependencyResolutionException;
use Windwalker\DI\Reflection\ReflectionCallable;
use Windwalker\Utilities\Assert\ArgumentsAssert;
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

    public function newInstance($class, array $args = [], int $options = 0)
    {
        if ($class instanceof DefinitionInterface) {
            return $this->container->resolve($class);
        }

        $options |= $this->container->getOptions();

        if (is_string($class)) {
            $builder = fn ($container, array $args, int $options) => $this->newInstanceByClassName($class, $args, $options);

            if (!($options & Container::IGNORE_ATTRIBUTES)) {
                $builder = $this->container->getAttributesResolver()
                    ->resolveObjectDecorate(new ReflectionClass($class), $builder);

                $instance = $builder($this->container, $args, $options);
            }
        } elseif (is_callable($class)) {
            $instance = $class($this->container, $args, $options);
        } else {
            throw new InvalidArgumentException(
                'New instance must get first argument as class name, callable or DefinitionInterface object.'
            );
        }

        if (!($options & Container::IGNORE_ATTRIBUTES)) {
            $instance = $this->container->getAttributesResolver()
                ->resolveProperties($instance);
        }

        return $instance;
    }

    public function newInstanceByClassName(string $class, array $args = [], $options = 0): object
    {
        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        // If there are no parameters, just return a new object.
        if (null === $constructor) {
            return new $class();
        }

        try {
            $args = array_merge($this->container->whenCreating($class)->getArguments(), $args);

            $args = $this->getMethodArgs($constructor, $args, $options);
        } catch (DependencyResolutionException $e) {
            throw new DependencyResolutionException(
                $e->getMessage() . ' - Target class: ' . $class,
                $e->getCode(),
                $e
            );
        }

        try {
            // Create a callable for the dataStore
            return $reflection->newInstanceArgs($args);
        } catch (\TypeError $e) {
            throw new DependencyResolutionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Build an array of constructor parameters.
     *
     * @param  ReflectionFunctionAbstract  $method  Method for which to build the argument array.
     * @param  array                       $args    The default args if class hint not provided.
     * @param  int                         $options
     *
     * @return array Array of arguments to pass to the method.
     *
     * @throws DependencyResolutionException
     * @throws ReflectionException
     * @since   2.0
     */
    protected function getMethodArgs(ReflectionFunctionAbstract $method, array $args = [], int $options = 0): array
    {
        $methodArgs = [];

        foreach ($method->getParameters() as $i => $param) {
            $dependencyVarName = $param->getName();

            // Prior (1): Handler ...$args
            if ($param->isVariadic()) {
                $trailing = [];

                foreach ($args as $key => $value) {
                    if (is_numeric($key)) {
                        $trailing[] = &$this->resolveParameterValue($value, $param);
                    }
                }

                $trailing   = array_slice($trailing, $i);
                $methodArgs = array_merge($methodArgs, $trailing);
                continue;
            }

            // Prior (2): Argument with numeric keys.
            if (array_key_exists($i, $args)) {
                $methodArgs[] = &$this->resolveParameterValue($args[$i], $param);
                continue;
            }

            // Prior (3): Argument with named keys.
            if (array_key_exists($dependencyVarName, $args)) {
                $methodArgs[] = &$this->resolveParameterValue($args[$dependencyVarName], $param);

                continue;
            }

            // Prior (4): Argument with numeric keys.
            $value = &$this->resolveParameterValue(
                $this->resolveParameterDependency($param, $args, $options),
                $param
            );

            if ($value !== null) {
                $methodArgs[] = &$value;
                continue;
            }

            if ($param->isOptional()) {
                // Finally, if there is a default parameter, use it.
                if ($param->isDefaultValueAvailable()) {
                    $methodArgs[] = $param->getDefaultValue();
                }

                continue;
            }

            $methodArgs[] = null;
        }

        return $methodArgs;
    }

    public function &resolveParameterDependency(\ReflectionParameter $param, array $args = [], int $options = 0)
    {
        $nope = null;
        $options |= $this->container->getOptions();

        $autowire = $options & Container::AUTO_WIRE;

        $type = $param->getType();
        $dependencyVarName = $param->getName();

        if (!$type) {
            return $nope;
        }

        if ($type instanceof \ReflectionUnionType) {
            $dependencies = $type->getTypes();
        } else {
            $dependencies = [$type];
        }

        foreach ($dependencies as $type) {
            $depObject           = null;
            $dependencyClassName = $type->getName();

            if (!class_exists($dependencyClassName)) {
                // Next dependency
                continue;
            }

            $dependency = new ReflectionClass($dependencyClassName);

            // If the dependency class name is registered with this container or a parent, use it.
            if ($this->container->has($dependencyClassName)) {
                $depObject = $this->container->get($dependencyClassName);
            } elseif (array_key_exists($dependencyVarName, $args)) {
                // If an arg provided, use it.
                return $args[$dependencyVarName];
            } elseif (
                $autowire
                && !$dependency->isAbstract()
                && !$dependency->isInterface()
                && !$dependency->isTrait()
            ) {
                // Otherwise we create this object recursive

                // Find child args if set
                if (isset($args[$dependencyClassName]) && is_array($args[$dependencyClassName])) {
                    $childArgs = $args[$dependencyClassName];
                } else {
                    $childArgs = [];
                }

                $depObject = $this->newInstance($dependencyClassName, $childArgs, $options);
            }

            if ($depObject instanceof $dependencyClassName) {
                return $depObject;
            }
        }

        return $nope;
    }

    /**
     * resolveArgumentValue
     *
     * @param  mixed                 $value
     * @param  \ReflectionParameter  $param
     * @param  int                   $options
     *
     * @return mixed
     *
     * @since  3.5.1
     */
    public function &resolveParameterValue(&$value, \ReflectionParameter $param, int $options = 0)
    {
        if ($value instanceof ObjectBuilderDefinition) {
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

        $options |= $this->container->getOptions();

        if (!($options & Container::IGNORE_ATTRIBUTES) && $param->getAttributes()) {
            $value = $this->container->getAttributesResolver()
                ->resolveParameter($value, $param);
        }

        return $value;
    }

    /**
     * Execute a callable with dependencies.
     *
     * @param  callable     $callable
     * @param  array        $args
     * @param  object|null  $context
     * @param  int          $options
     *
     * @return mixed
     *
     * @throws ReflectionException
     */
    public function call(callable $callable, array $args = [], ?object $context = null, int $options = 0)
    {
        $ref = new ReflectionCallable($callable);

        $callable = $ref->getClosure();

        if ($context) {
            $callable = $callable->bindTo($context, $context);
        }

        $closure = function (Container $container, array $args, int $options) use ($callable, $ref) {
            $args = $this->getMethodArgs($ref->getReflector(), $args, $options);

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

        $options |= $this->container->getOptions();

        if (!($options & Container::IGNORE_ATTRIBUTES)) {
            $closure = $this->container->getAttributesResolver()
                ->resolveCallable($ref->getReflector(), $closure);
        }

        return $closure($this->container, $args, $options);
    }
}
