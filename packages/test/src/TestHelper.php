<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\Test;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Static helper methods to assist unit testing PHP code.
 *
 * @note   This class is based on Joomla Test package.
 *
 * @since  2.0
 */
class TestHelper
{
    /**
     * Helper method that gets a protected or private property in a class by relfection.
     *
     * @param  object  $object        The object from which to return the property value.
     * @param  string  $propertyName  The name of the property to return.
     *
     * @return  mixed  The value of the property.
     *
     * @throws ReflectionException
     * @since   2.0
     */
    public static function getValue(object $object, string $propertyName)
    {
        $ref = new ReflectionClass($object);

        // First check if the property is easily accessible.
        if ($ref->hasProperty($propertyName)) {
            $property = $ref->getProperty($propertyName);
            $property->setAccessible(true);

            return $property->getValue($object);
        }

        // Hrm, maybe dealing with a private property in the parent class.
        if (get_parent_class($object)) {
            $property = new ReflectionProperty(get_parent_class($object), $propertyName);
            $property->setAccessible(true);

            return $property->getValue($object);
        }

        throw new InvalidArgumentException(
            sprintf(
                'Invalid property [%s] for class [%s]',
                $propertyName,
                get_class($object)
            )
        );
    }

    /**
     * Helper method that invokes a protected or private method in a class by reflection.
     *
     * Example usage:
     *
     * $this->asserTrue(TestCase::invoke('methodName', $this->object, 123));
     *
     * @param  object  $object      The object on which to invoke the method.
     * @param  string  $methodName  The name of the method to invoke.
     * @param  array   $args        Arguments.
     *
     * @return  mixed
     *
     * @throws ReflectionException
     * @since   2.0
     */
    public static function invoke(object $object, string $methodName, ...$args)
    {
        $method = new ReflectionMethod($object, $methodName);
        $method->setAccessible(true);

        return $method->invokeArgs(is_object($object) ? $object : null, $args);
    }

    /**
     * Helper method that sets a protected or private property in a class by relfection.
     *
     * @param  object  $object        The object for which to set the property.
     * @param  string  $propertyName  The name of the property to set.
     * @param  mixed   $value         The value to set for the property.
     *
     * @return  void
     *
     * @throws ReflectionException
     * @since   2.0
     */
    public static function setValue(object $object, string $propertyName, $value): void
    {
        $refl = new ReflectionClass($object);

        // First check if the property is easily accessible.
        if ($refl->hasProperty($propertyName)) {
            $property = $refl->getProperty($propertyName);
            $property->setAccessible(true);

            $property->setValue($object, $value);
        } elseif (get_parent_class($object)) {
            // Hrm, maybe dealing with a private property in the parent class.
            $property = new ReflectionProperty(get_parent_class($object), $propertyName);
            $property->setAccessible(true);

            $property->setValue($object, $value);
        }
    }

    public static function getProperties(
        object $object,
        int $filters = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED
    ): array {
        $ref = new ReflectionClass($object);

        $properties = $ref->getProperties($filters);

        $values = [];

        foreach ($properties as $property) {
            $property->setAccessible(true);

            $values[$property->getName()] = $property->getValue($object);
        }

        return $values;
    }
}
