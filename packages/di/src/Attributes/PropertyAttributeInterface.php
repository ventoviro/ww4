<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2020 .
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\DI\Attributes;

use Windwalker\DI\Container;

/**
 * Interface PropertyAnnotationInterface
 */
interface PropertyAttributeInterface
{
    /**
     * handle
     *
     * @param Container            $container
     * @param object               $instance
     * @param \ReflectionProperty  $reflector
     *
     * @return  object
     */
    public function __invoke(Container $container, object $instance, \ReflectionProperty $reflector);
}
