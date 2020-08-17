<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2020 .
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\DI\Attributes;

use Windwalker\DI\Container;

/**
 * Interface PropertyAnnotationInterface
 *
 * @since  3.5.19
 */
interface PropertyAttributeInterface
{
    /**
     * handle
     *
     * @param Container           $container
     * @param object              $instance
     * @param \ReflectionProperty $property
     *
     * @return  object
     *
     * @since  3.5.19
     */
    public function __invoke(Container $container, object $instance, \ReflectionProperty $property);
}
