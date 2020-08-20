<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Inject;
use Windwalker\DI\Definition\DefinitionFactory;
use Windwalker\DI\Definition\DefinitionInterface;
use Windwalker\DI\Definition\ObjectBuilderDefinition;
use Windwalker\DI\Definition\StoreDefinitionInterface;
use Windwalker\DI\Exception\DefinitionException;
use Windwalker\DI\Exception\DefinitionNotFoundException;
use Windwalker\Utilities\Contract\ArrayAccessibleInterface;

/**
 * The Container class.
 */
class Container implements ContainerInterface, \IteratorAggregate, \Countable, ArrayAccessibleInterface
{
    public const SHARED = 1 << 0;
    public const PROTECTED = 1 << 1;
    public const AUTO_WIRE = 1 << 2;
    public const IGNORE_ATTRIBUTES = 1 << 3;

    protected int $options = 0;

    /**
     * Holds the key aliases.
     *
     * @var    array $aliases
     * @since  2.0
     */
    protected array $aliases = [];

    protected array $storage = [];

    /**
     * Parent for hierarchical containers.
     *
     * @var  Container|null
     */
    protected ?Container $parent = null;

    /**
     * Property parameters.
     *
     * @var Collection
     */
    protected Collection $parameters;

    /**
     * @var ObjectBuilderDefinition[]
     */
    protected array $builders = [];

    protected DependencyResolver $dependencyResolver;

    protected AttributesResolver $attributesResolver;

    /**
     * Container constructor.
     *
     * @param  Container|null  $parent
     * @param  int             $options
     */
    public function __construct(?Container $parent = null, int $options = 0)
    {
        $this->parent = $parent;
        $this->options = $options;
        $this->parameters = new Collection();

        $this->dependencyResolver = new DependencyResolver($this);
        $this->attributesResolver = new AttributesResolver($this);
    }

    /**
     * set
     *
     * @param  string  $id
     * @param  mixed   $value
     * @param  int     $options
     *
     * @return Container
     * @throws DefinitionException
     */
    public function set(string $id, $value, int $options = 0)
    {
        $definition = $this->getDefinition($id);

        if ($definition && $definition->isProtected()) {
            throw new DefinitionException(
                sprintf(
                    'Container id: %s is protected.',
                    $id
                )
            );
        }

        $this->setDefinition($id, DefinitionFactory::create($value, $options));

        return $this;
    }

    /**
     * share
     *
     * @param  string  $id
     * @param  mixed   $value
     * @param  int     $options
     *
     * @return static
     * @throws DefinitionException
     */
    public function share(string $id, $value, int $options = 0)
    {
        return $this->set($id, $value, $options | static::SHARED);
    }

    /**
     * protect
     *
     * @param  string  $id
     * @param  mixed   $value
     * @param  int     $options
     *
     * @return static
     * @throws DefinitionException
     */
    public function protect(string $id, $value, int $options = 0)
    {
        return $this->set($id, $value, $options | static::PROTECTED);
    }

    /**
     * setDefinition
     *
     * @param  string                    $id
     * @param  StoreDefinitionInterface  $value
     *
     * @return  $this
     */
    public function setDefinition(string $id, StoreDefinitionInterface $value)
    {
        $this->storage[$id] = $value;

        // 3.2 Remove alias
        $this->removeAlias($id);

        return $this;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param  string  $id        Identifier of the entry to look for.
     * @param  bool    $forceNew  True to force creation and return of a new instance.
     *
     * @return mixed Entry.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     */
    public function get($id, bool $forceNew = false)
    {
        $definition = $this->getDefinition($id);

        if ($definition === null) {
            throw new DefinitionNotFoundException(
                sprintf('Key %s has not been registered with the container.', $id)
            );
        }

        if ($forceNew) {
            $definition->reset();
        }

        return $definition->resolve($this);
    }

    public function resolve($idOrDefinition, bool $forceNew = false)
    {
        if ($idOrDefinition instanceof DefinitionInterface) {
            return $idOrDefinition->resolve($this);
        }

        return $this->get($idOrDefinition, $forceNew);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param  string  $id  Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id): bool
    {
        return $this->getDefinition($id) !== null;
    }

    /**
     * Remove an item from container.
     *
     * @param  string  $id  Name of the dataStore key to get.
     *
     * @return  static  This object for chaining.
     *
     * @since   2.1
     */
    public function remove(string $id)
    {
        $id = $this->resolveAlias($id);

        unset($this->storage[$id]);

        return $this;
    }

    /**
     * Fork an instance to a new key.
     *
     * @param  string  $id        Origin key.
     * @param  string  $newId     New key.
     * @param  bool    $forceNew  Force new.
     *
     * @return  mixed  Forked instance.
     *
     * @since   2.0.7
     */
    public function fork(string $id, string $newId, bool $forceNew = false)
    {
        $raw = clone $this->getDefinition($id);

        $this->storage[$newId] = $raw;

        return $this->get($newId, $forceNew);
    }

    /**
     * Get the raw data assigned to a key.
     *
     * @param  string  $id  The key for which to get the stored item.
     *
     * @return  ?StoreDefinitionInterface
     *
     * @since   2.0
     */
    public function getDefinition($id): ?StoreDefinitionInterface
    {
        $id = $this->resolveAlias($id);

        if ($this->storage[$id] ?? null) {
            return $this->storage[$id];
        }

        if ($this->parent instanceof static) {
            return $this->parent->getDefinition($id);
        }

        return null;
    }

    public function clear(): void
    {
        $this->storage = [];
        $this->builders = [];
        $this->aliases = [];
        $this->parameters->reset();
    }

    /**
     * wrapDefinition
     *
     * @param  string                               $id
     * @param  DefinitionInterface|string|\Closure  $definition
     *
     * @return  $this
     */
    // public function wrapDefinition(string $id, DefinitionInterface|\Closure|string $definition)
    // {
    //     $def = $this->getDefinition($id);
    //
    //     if (!$id) {
    //         throw new DefinitionNotFoundException("Key: $id not found in container.");
    //     }
    //
    //     if ($definition instanceof \Closure) {
    //         $definition = $definition($def, $this);
    //     } elseif (is_string($definition) && class_exists($definition)) {
    //         $definition = new $definition($def);
    //     }
    //
    //     $this->setDefinition($id, $definition);
    //
    //     return $this;
    // }

    /**
     * Bind a class or key to another instance, container will return instance if it has been set
     * or created, otherwise it will create new one.
     *
     * @param  string  $id
     * @param  mixed   $class
     * @param  int     $options
     *
     * @return Container
     *
     * @throws DefinitionException
     * @since   3.0
     */
    public function bind(string $id, string $class, int $options = 0)
    {
        $class = static fn(Container $container) => $container->newInstance($class, [], $options);

        return $this->set($id, $class, $options);
    }

    /**
     * bindShared
     *
     * @param  string  $id
     * @param  mixed   $class
     * @param  int     $options
     *
     * @return Container
     *
     * @throws DefinitionException
     * @since   3.0
     */
    public function bindShared(string $id, string $class, int $options = 0)
    {
        return $this->bind($id, $class, $options | static::SHARED);
    }

    /**
     * prepareObject
     *
     * @param  string         $class
     * @param  \Closure|null  $extend
     * @param  int            $options
     *
     * @return Container
     *
     * @throws DefinitionException
     * @since   3.0
     */
    public function prepareObject(string $class, ?\Closure $extend = null, int $options = 0)
    {
        $handler = static fn(Container $container) => $container->newInstance($class, [], $options);

        $this->set($class, $handler, $options);

        if (is_callable($extend)) {
            $this->extend($class, $extend);
        }

        return $this;
    }

    /**
     * prepareSharedObject
     *
     * @param  string         $class
     * @param  \Closure|null  $extend
     * @param  int            $options
     *
     * @return Container
     *
     * @throws DefinitionException
     * @since   3.0
     */
    public function prepareSharedObject(string $class, \Closure $extend = null, int $options = 0)
    {
        return $this->prepareObject($class, $extend, $options | static::SHARED);
    }

    /**
     * Extend a defined service Closure by wrapping the existing one with a new Closure.  This
     * works very similar to a decorator pattern.  Note that this only works on service Closures
     * that have been defined in the current Provider, not parent providers.
     *
     * @param  string    $id       The unique identifier for the Closure or property.
     * @param  \Closure  $closure  A Closure to wrap the original service Closure.
     *
     * @return  static
     *
     * @throws  \InvalidArgumentException
     * @since   2.0
     */
    public function extend(string $id, \Closure $closure)
    {
        $definition = $this->getDefinition($id);

        if ($definition === null) {
            throw new \UnexpectedValueException(
                sprintf('The requested id %s does not exist to extend.', $id)
            );
        }

        $definition->extend($closure);

        return $this;
    }

    /**
     * createObject
     *
     * @param  string  $class
     * @param  array   $args
     * @param  int     $options
     *
     * @return mixed
     * @throws DefinitionException
     * @since   3.0
     */
    public function createObject(string $class, array $args = [], int $options = 0)
    {
        $callback = fn(Container $container) => $container->newInstance($class, $args, $options);

        return $this->set($class, $callback, $options)->get($class);
    }

    /**
     * createSharedObject
     *
     * @param  string  $class
     * @param  array   $args
     * @param  int     $options
     *
     * @return mixed
     *
     * @throws DefinitionException
     * @since   3.0
     */
    public function createSharedObject(string $class, array $args = [], int $options = 0)
    {
        return $this->createObject($class, $args, $options | static::SHARED);
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
     * @throws \ReflectionException
     */
    public function call(callable $callable, array $args = [], ?object $context = null, int $options = 0)
    {
        return $this->dependencyResolver->call($callable, $args, $context, $options);
    }

    /**
     * whenCreating
     *
     * @param  string  $class
     *
     * @return  ObjectBuilderDefinition
     */
    public function whenCreating(string $class): ObjectBuilderDefinition
    {
        $builder = $this->builders[$class] ??= new ObjectBuilderDefinition($class, $this);

        // if (!$this->has($class)) {
        //     $this->setDefinition($class, new ObjectBuilderDefinition($builder));
        // }

        return $builder;
    }

    public function newInstance($class, array $args = [], int $options = 0)
    {
        return $this->dependencyResolver->newInstance($class, $args, $options);
    }

    /**
     * Register a service provider to the container.
     *
     * @param   ServiceProviderInterface $provider The service provider to register.w
     *
     * @return  static  This object for chaining.
     *
     * @since   2.0
     */
    public function registerServiceProvider(ServiceProviderInterface $provider)
    {
        $provider->register($this);

        return $this;
    }

    /**
     * Create an alias for a given key for easy access.
     *
     * @param  string  $alias  The alias name
     * @param  string  $id     The key to alias
     *
     * @return  static  This object for chaining.
     *
     * @since   2.0
     */
    public function alias(string $alias, string $id)
    {
        $this->aliases[$alias] = $id;

        return $this;
    }

    /**
     * Search the aliases property for a matching alias key.
     *
     * @param  string  $id  The key to search for.
     *
     * @return  string
     *
     * @since   2.0
     */
    protected function resolveAlias($id)
    {
        while (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }

        return $id;
    }

    /**
     * Remove an alias.
     *
     * @param  string  $alias  The alias name to remove.
     *
     * @return  static Support chaining.
     *
     * @since  3.2
     */
    public function removeAlias(string $alias)
    {
        unset($this->aliases[$alias]);

        return $this;
    }

    /**
     * Retrieve an external iterator
     *
     * @return \Traversable An instance of an object implementing Iterator or Traversable
     *
     * @since   2.1
     */
    public function &getIterator(): \Generator
    {
        foreach ($this->storage as $id => &$definition) {
            yield $id => $definition;
        }
    }

    /**
     * Create a child Container with a new property scope that
     * that has the ability to access the parent scope when resolving.
     *
     * @return  static  The new container object.
     *
     * @since   2.0
     */
    public function createChild()
    {
        return new static($this);
    }

    /**
     * getParents
     *
     * @return  Container[]
     */
    public function getParents(): array
    {
        $parents = [];

        $parent = $this->getParent();

        while ($parent) {
            $parents[] = $parent;
            $parent    = $this->getParent();
        }

        return $parents;
    }

    /**
     * @return Container|null
     */
    public function getParent(): ?Container
    {
        return $this->parent;
    }

    /**
     * @param  Container|null  $parent
     *
     * @return  static  Return self to support chaining.
     */
    public function setParent(?Container $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    /**
     * @param  Collection  $parameters
     *
     * @return  static  Return self to support chaining.
     */
    public function setParameters(Collection $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Count elements of an object
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */
    public function count(): int
    {
        return count($this->storage);
    }

    /**
     * @return int
     */
    public function getOptions(): int
    {
        return $this->options;
    }

    /**
     * @param  int  $options
     *
     * @return  static  Return self to support chaining.
     */
    public function setOptions(int $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return AttributesResolver
     */
    public function getAttributesResolver(): AttributesResolver
    {
        return $this->attributesResolver;
    }

    /**
     * Returns whether the requested key exists
     *
     * @param  mixed  $id
     *
     * @return bool
     */
    public function offsetExists($id): bool
    {
        return $this->has($id);
    }

    /**
     * Returns the value at the specified key
     *
     * @param  mixed  $id
     *
     * @return mixed
     */
    public function &offsetGet($id)
    {
        $item = $this->get($id);

        return $item;
    }

    /**
     * Sets the value at the specified key to value
     *
     * @param  mixed  $id
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($id, $value): void
    {
        $this->set($id, $value);
    }

    /**
     * Unsets the value at the specified key
     *
     * @param  mixed  $id
     *
     * @return void
     */
    public function offsetUnset($id): void
    {
        $this->remove($id);
    }

    /**
     * @return DependencyResolver
     */
    public function getDependencyResolver(): DependencyResolver
    {
        return $this->dependencyResolver;
    }

    /**
     * @param  DependencyResolver  $dependencyResolver
     *
     * @return  static  Return self to support chaining.
     */
    public function setDependencyResolver(DependencyResolver $dependencyResolver)
    {
        $this->dependencyResolver = $dependencyResolver;

        return $this;
    }

    /**
     * @param  AttributesResolver  $attributesResolver
     *
     * @return  static  Return self to support chaining.
     */
    public function setAttributesResolver(AttributesResolver $attributesResolver)
    {
        $this->attributesResolver = $attributesResolver;

        return $this;
    }
}
