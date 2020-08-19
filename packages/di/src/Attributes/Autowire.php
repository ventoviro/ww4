<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Attributes;

use Windwalker\DI\Container;

/**
 * The Autowire class.
 */
@@\Attribute
class Autowire implements ContainerAttributeInterface
{
    /**
     * __invoke
     *
     * @param  Container   $container
     * @param  \Closure    $closure
     * @param  \Reflector  $reflector
     *
     * @return  \Closure
     */
    public function __invoke(Container $container, $closure, \Reflector $reflector)
    {
        if ($closure === null && $reflector instanceof \ReflectionParameter && $reflector->getType()) {
            $resolver = $container->getDependencyResolver();

            return $resolver->resolveParameterValue(
                $resolver->resolveParameterDependency($reflector, [], Container::AUTO_WIRE),
                $reflector,
                Container::IGNORE_ATTRIBUTES
            );
        }

        return fn(
            Container $container,
            array $args,
            int $options
        ) => $closure($container, $args, $options | Container::AUTO_WIRE);
    }
}
