<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI;

/**
 * The AttributesResolver class.
 */
class AttributesResolver
{
    public const CLASSES = 'classes';
    public const PROPERTIES = 'properties';
    public const METHODS = 'methods';
    public const PARAMETERS = 'parameters';

    protected Container $container;

    protected array $registry = [
        self::CLASSES => [],
        self::PROPERTIES => [],
        self::METHODS => [],
        self::PARAMETERS => [],
    ];

    /**
     * AttributesResolver constructor.
     *
     * @param  Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function resolveClass(object $instance): object
    {
        $ref = new \ReflectionClass($instance);

        foreach ($ref->getAttributes() as $attribute) {
            if ($this->hasAttribute($attribute->getName(), static::CLASSES)) {
                $instance = $this->runAttribute($attribute, $instance, $ref) ?? $instance;
            }
        }

        return $instance;
    }

    public function resolveMethod(string $methodName, \Closure $closure): \Closure
    {
        $ref = new \ReflectionFunction($closure);

        $method = new \ReflectionMethod($ref->getClosureThis(), $methodName);

        foreach ($method->getAttributes() as $attribute) {
            if ($this->hasAttribute($attribute->getName(), static::METHODS)) {
                $closure = $this->runAttribute($attribute, $closure, $method) ?? $closure;
            }
        }

        return $closure;
    }

    public function resolveParameter(object $instance, string $parameterName, \Closure $closure): \Closure
    {
        $ref = new \ReflectionParameter($closure, $parameterName);

        foreach ($ref->getAttributes() as $attribute) {
            if ($this->hasAttribute($attribute->getName(), static::METHODS)) {
                $closure = $this->runAttribute($attribute, $closure, $ref) ?? $closure;
            }
        }

        return $closure;
    }

    public function resolveProperties(object $instance): object
    {
        $ref = new \ReflectionObject($instance);

        foreach ($ref->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                if ($this->hasAttribute($attribute->getName(), static::PROPERTIES)) {
                    $instance = $this->runAttribute($attribute, $instance, $property) ?? $instance;
                }
            }
        }

        return $instance;
    }

    public function hasAttribute(string $attributeClass, string $type): bool
    {
        return in_array(strtolower($attributeClass), $this->registry[$type], true);
    }

    public function registerAttribute(string $attributeClass, string $type): void
    {
        if (!$this->hasAttribute($attributeClass, $type)) {
            $this->registry[$type][] = strtolower($attributeClass);
        }
    }

    protected function runAttribute(\ReflectionAttribute $attribute, ...$args)
    {
        /** @var callable|object $attrInstance */
        $attrInstance = $attribute->newInstance();

        if (!is_callable($attrInstance)) {
            $class = get_class($attribute);
            throw new \LogicException("Annotation: {$class} is not invokable.");
        }

        return $attrInstance($this->container, ...$args);
    }

    protected static function normalizeClassName(string $className): string
    {
        return strtolower(trim($className, '\\'));
    }
}
