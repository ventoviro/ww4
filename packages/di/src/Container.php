<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI;

use DI\Definition\ObjectDefinition;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Windwalker\Data\Collection;
use Windwalker\DI\Builder\ObjectBuilder;
use Windwalker\DI\Definition\DefinitionFactory;
use Windwalker\DI\Definition\DefinitionInterface;
use Windwalker\DI\Definition\NoCacheDefinition;
use Windwalker\DI\Definition\ObjectBuilderDefinition;
use Windwalker\DI\Exception\DefinitionNotFoundException;
use Windwalker\DI\Exception\DependencyResolutionException;

/**
 * The Container class.
 */
class Container implements ContainerInterface, \IteratorAggregate, \Countable
{
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
     * @var ObjectBuilder[]
     */
    protected array $builders = [];

    /**
     * Container constructor.
     *
     * @param  Container|null  $parent
     */
    public function __construct(?Container $parent)
    {
        $this->parent = $parent;
    }

    /**
     * set
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return  static
     */
    public function set(string $key, $value)
    {
        $this->setDefinition(
            $key,
            new NoCacheDefinition(
                DefinitionFactory::create($value),
            )
        );

        // 3.2 Remove alias
        $this->removeAlias($key);

        return $this;
    }

    public function setDefinition(string $key, DefinitionInterface $value)
    {
        $this->storage[$key] = $value;

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

        return $definition->resolve($this, $forceNew);
    }

    public function resolve($idOrDefinition, bool $forceNew = false)
    {
        if ($idOrDefinition instanceof DefinitionInterface) {
            return $idOrDefinition->resolve($this, $forceNew);
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
     * @param  string  $key  Name of the dataStore key to get.
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
     * @param  string  $key  The key for which to get the stored item.
     *
     * @return  ?DefinitionInterface
     *
     * @since   2.0
     */
    protected function getDefinition($key): ?DefinitionInterface
    {
        $key = $this->resolveAlias($key);

        if ($this->storage[$key] ?? null) {
            return $this->storage[$key];
        }

        if ($this->parent instanceof static) {
            return $this->parent->getDefinition($key);
        }

        return null;
    }

    public function bind(string $name, $value)
    {

    }

    public function singleton(string $key, $value)
    {
        if (is_string($value)) {
            $value = fn (Container $container) => $container->newInstance($key);
        }

        $this->set($key, DefinitionFactory::create($value));

        return $this;
    }

    /**
     * Execute a callable with dependencies.
     *
     * @param callable $callable
     * @param array    $args
     * @param object   $context
     *
     * @return  mixed
     *
     * @throws DependencyResolutionException
     * @throws \ReflectionException
     */
    public function call(callable $callable, array $args = [], object $context = null)
    {
        return DependencyResolver::call($this, $callable, $args, $context);
    }

    /**
     * whenCreating
     *
     * @param   string $class
     *
     * @return  ObjectBuilder
     */
    public function whenCreating(string $class): ObjectBuilder
    {
        $builder = $this->builders[$class] ??= new ObjectBuilder($class, $this);

        if (!$this->has($class)) {
            $this->setDefinition($class, new ObjectBuilderDefinition($builder));
        }

        return $builder;
    }

    public function newInstance($class, array $args = [])
    {
        return DependencyResolver::newInstance($this, $class, $args);
    }

    // protected function resolveBuilderDefinition(ObjectBuilderDefinition $def, array $args)
    // {
    //     $def = clone $def;
    //     $def->setArguments($args);
    //
    //     return $this->resolve($def);
    // }

    /**
     * Create an alias for a given key for easy access.
     *
     * @param  string  $alias  The alias name
     * @param  string  $key    The key to alias
     *
     * @return  static  This object for chaining.
     *
     * @since   2.0
     */
    public function alias(string $alias, string $key)
    {
        $this->aliases[$alias] = $key;

        return $this;
    }

    /**
     * Search the aliases property for a matching alias key.
     *
     * @param  string  $key  The key to search for.
     *
     * @return  string
     *
     * @since   2.0
     */
    protected function resolveAlias($key)
    {
        while (isset($this->aliases[$key])) {
            $key = $this->aliases[$key];
        }

        return $key;
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
    public function getIterator(): \Generator
    {
        foreach ($this->storage as $id => $definition) {
            yield $id => $definition;
        }
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
}
