<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\DI\Attributes;

use Attribute;
use Windwalker\DI\Container;
use Windwalker\DI\Exception\DependencyResolutionException;

/**
 * The Inject class.
 *
 * @since  3.4.4
 */
@@Attribute
class Inject implements PropertyDecoratorInterface
{
    protected ?string $id = null;

    protected bool $forceNew = false;

    /**
     * Inject constructor.
     *
     * @param  string|null  $id
     * @param  bool         $forceNew
     */
    public function __construct(?string $id = null, bool $forceNew = false)
    {
        $this->id       = $id;
        $this->forceNew = $forceNew;
    }

    /**
     * __invoke
     *
     * @param  Container            $container
     * @param  object               $instance
     * @param  \ReflectionProperty  $reflector
     *
     * @return  object
     *
     * @throws DependencyResolutionException
     */
    public function __invoke(Container $container, object $instance, \ReflectionProperty $reflector)
    {
        if (!$reflector instanceof \ReflectionProperty) {
            return $instance;
        }

        $type = $reflector->getType();

        if (!$type) {
            throw new DependencyResolutionException(
                sprintf(
                    'Property: %s->%s inject with no type.',
                    $reflector->getDeclaringClass()->getName(),
                    $reflector->getName()
                )
            );
        }

        if ($type instanceof \ReflectionUnionType) {
            $types = [$type->getTypes()];
        } else {
            $types = [$type];
        }

        $varClass = null;

        foreach ($types as $type) {
            if (class_exists($type->getName())) {
                $varClass = $type->getName();
                break;
            }
        }

        if (!$varClass) {
            throw new DependencyResolutionException(
                sprintf('unable to resolve injection of property: "%s".', $reflector->getName())
            );
        }

        if ($reflector->isProtected() || $reflector->isPrivate()) {
            $reflector->setAccessible(true);
        }

        $reflector->setValue(
            $instance,
            $this->resolveInjectable($container, $varClass)
        );

        if ($reflector->isProtected() || $reflector->isPrivate()) {
            $reflector->setAccessible(false);
        }

        return $instance;
    }

    /**
     * getInjectable
     *
     * @param  Container  $container
     * @param  string     $class
     *
     * @return  mixed
     *
     * @throws DependencyResolutionException
     */
    public function resolveInjectable(Container $container, $class)
    {
        $id = $this->id ?? $class;

        if ($container->has($id)) {
            return $container->get($id, $this->forceNew);
        }

        if (!class_exists($id)) {
            throw new DependencyResolutionException(
                sprintf('Class: "%s" not exists.', $id)
            );
        }

        return $container->newInstance($id);
    }
}
