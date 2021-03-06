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
    protected Container $container;

    protected array $registry = [
        Attributes\AttributeType::CLASSES => [],
        Attributes\AttributeType::PROPERTIES => [],
        Attributes\AttributeType::FUNCTION_METHOD => [],
        Attributes\AttributeType::PARAMETERS => [],
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

    public function resolveObjectDecorate(\ReflectionClass $ref, \Closure $builder): \Closure
    {
        foreach ($ref->getAttributes() as $attribute) {
            if ($this->hasAttribute($attribute->getName(), Attributes\AttributeType::CLASSES)) {
                $builder = $this->runAttribute($attribute, $builder, $ref) ?? $builder;
            }
        }

        return $builder;
    }

    public function resolveCallable(\ReflectionFunctionAbstract $ref, \Closure $closure): \Closure
    {
        foreach ($ref->getAttributes() as $attribute) {
            if ($this->hasAttribute($attribute->getName(), Attributes\AttributeType::FUNCTION_METHOD)) {
                $closure = $this->runAttribute($attribute, $closure, $ref) ?? $closure;
            }
        }

        return $closure;
    }

    public function resolveParameter($value, \ReflectionParameter $ref)
    {
        foreach ($ref->getAttributes() as $attribute) {
            if ($this->hasAttribute($attribute->getName(), Attributes\AttributeType::PARAMETERS)) {
                $value = $this->runAttribute($attribute, $value, $ref);
            }
        }

        return $value;
    }

    public function resolveProperties(object $instance): object
    {
        $ref = new \ReflectionObject($instance);

        foreach ($ref->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                if ($this->hasAttribute($attribute->getName(), Attributes\AttributeType::PROPERTIES)) {
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

    public function registerAttribute(string $attributeClass, array|string $types): void
    {
        $types = (array) $types;

        foreach ($types as $type) {
            if (!$this->hasAttribute($attributeClass, $type)) {
                $this->registry[$type][] = strtolower($attributeClass);
            }
        }
    }

    public function removeAttribute(string $attributeClass, string $type): void
    {
        unset($this->registry[$type][strtolower($attributeClass)]);
    }

    protected function runAttribute(\ReflectionAttribute $attribute, ...$args)
    {
        /** @var callable|object $attrInstance */
        $attrInstance = $attribute->newInstance();

        if (!is_callable($attrInstance)) {
            $class = get_class($attribute);
            throw new \LogicException("Attribute: {$class} is not invokable.");
        }

        return $attrInstance($this->container, ...$args);
    }

    protected static function normalizeClassName(string $className): string
    {
        return strtolower(trim($className, '\\'));
    }
}
