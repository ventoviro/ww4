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

    /**
     * AttributesResolver constructor.
     *
     * @param  Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function resolvePropertiesAttributes(object $instance)
    {
        $ref = new \ReflectionObject($instance);

        foreach ($ref->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                // todo: use built-in newInstance after Attribute stable

                $attrInstance = $attribute->newInstance();

                if (!is_callable($attrInstance)) {
                    $class = get_debug_type($attrInstance);
                    throw new \LogicException("Attribute: {$class} is not callable.");
                }

                $instance = $attrInstance($this->container, $instance, $property) ?? $instance;
            }
        }
    }
}
