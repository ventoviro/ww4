<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use JsonSerializable;
use Windwalker\Scalars\Concern\ArrayContentTrait;
use Windwalker\Scalars\Concern\ArrayCreationTrait;
use Windwalker\Scalars\Concern\ArrayLoopTrait;
use Windwalker\Scalars\Concern\ArrayModifyTrait;
use Windwalker\Scalars\Concern\ArraySortTrait;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Contract\DumpableInterface;
use Windwalker\Utilities\TypeCast;
use function Windwalker\str;

/**
 * The ArrayObject class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArrayObject implements
    DumpableInterface,
    Countable,
    ArrayAccess,
    IteratorAggregate,
    JsonSerializable
{
    use ArraySortTrait;
    use ArrayCreationTrait;
    use ArrayModifyTrait;
    use ArrayLoopTrait;
    use ArrayContentTrait;

    public const GROUP_TYPE_ARRAY = Arr::GROUP_TYPE_ARRAY;

    public const GROUP_TYPE_KEY_BY = Arr::GROUP_TYPE_KEY_BY;

    public const GROUP_TYPE_MIX = Arr::GROUP_TYPE_MIX;

    protected array $storage = [];

    /**
     * ArrayObject constructor.
     *
     * @param  array  $storage
     */
    public function __construct($storage = [])
    {
        $this->storage = TypeCast::toArray($storage);
    }

    public static function explode(string $delimiter, string $string, ?int $limit = null): self
    {
        return new static(explode(...func_get_args()));
    }

    /**
     * bindNewInstance
     *
     * @param  mixed  $data
     *
     * @return  static
     */
    protected static function newInstance($data = []): self
    {
        return new static($data);
    }

    /**
     * keys
     *
     * @param  string|int|null  $search
     * @param  bool|null        $strict
     *
     * @return  static
     *
     * @since  3.5
     */
    public function keys($search = null, ?bool $strict = null): self
    {
        if (func_get_args()[0] ?? false) {
            return static::newInstance(array_keys($this->storage, $search, (bool) $strict));
        }

        return static::newInstance(array_keys($this->storage));
    }

    /**
     * column
     *
     * @param  string|int   $name
     * @param  string|null  $key
     *
     * @return  static
     *
     * @since  3.5
     */
    public function column($name, ?string $key = null): self
    {
        return new static(array_column($this->storage, $name, $key));
    }

    /**
     * setColumn
     *
     * @param  string|int  $name
     * @param  mixed       $value
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setColumn($name, $value): self
    {
        return static::newInstance()->apply(function (array $storage) use ($name, $value) {
            foreach ($this->storage as $item) {
                if (Arr::isAccessible($item)) {
                    $item[$name] = $value;
                } elseif (is_object($item)) {
                    $item->$name = $value;
                }
            }
        });
    }

    /**
     * apply
     *
     * @param  callable  $callback
     *
     * @return  static
     */
    public function apply(callable $callback): self
    {
        return static::newInstance($callback(TypeCast::toArray($this)));
    }

    /**
     * values
     *
     * @return  static
     */
    public function values(): self
    {
        return static::newInstance(array_values(TypeCast::toArray($this)));
    }

    /**
     * pipe
     *
     * @param  callable  $callback
     *
     * @return  static
     */
    public function pipe(callable $callback): self
    {
        return $callback($this);
    }

    /**
     * Returns whether the requested key exists
     *
     * @param  mixed  $key
     *
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Sets the value at the specified key to value
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return void|mixed
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Unsets the value at the specified key
     *
     * @param  mixed  $key
     *
     * @return void|mixed
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Returns the value at the specified key by reference
     *
     * @param  mixed  $key
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function &__get($key)
    {
        $ret =& $this->offsetGet($key);

        return $ret;
    }

    /**
     * Get the number of public properties in the ArrayObject
     *
     * @return int
     */
    public function count()
    {
        return count($this->storage);
    }

    /**
     * Creates a copy of the ArrayObject.
     *
     * @param  bool  $recursive
     *
     * @param  bool  $onlyDumpable
     *
     * @return array
     */
    public function dump(bool $recursive = false, bool $onlyDumpable = false): array
    {
        if (!$recursive) {
            return $this->storage;
        }

        return TypeCast::toArray($this->storage, true);
    }

    /**
     * search
     *
     * @param  mixed  $value
     * @param  bool   $strict
     *
     * @return  false|int|string
     *
     * @since  3.5
     */
    public function search($value, bool $strict = false)
    {
        return array_search($value, $this->storage, $strict);
    }

    /**
     * indexOf
     *
     * @param  mixed  $value
     * @param  bool   $strict
     *
     * @return  int
     *
     * @since  3.5.2
     */
    public function indexOf($value, bool $strict = false): int
    {
        $r = $this->search($value, $strict);

        return (int) ($r === false ? -1 : $r);
    }

    /**
     * sum
     *
     * @return  float|int
     *
     * @since  3.5
     */
    public function sum()
    {
        return array_sum($this->storage);
    }

    /**
     * unique
     *
     * @param  int  $sortFlags
     *
     * @return  static
     *
     * @since  3.5
     */
    public function unique($sortFlags = SORT_STRING): self
    {
        return new static(array_unique($this->storage, $sortFlags));
    }

    /**
     * current
     *
     * @param  mixed  $value
     * @param  bool   $strict
     *
     * @return  bool
     *
     * @since  3.5
     */
    public function contains($value, bool $strict = false): bool
    {
        return (bool) in_array($value, $this->storage, $strict);
    }

    /**
     * keyExists
     *
     * @param  mixed  $key
     *
     * @return  bool
     *
     * @since  3.5
     */
    public function keyExists($key): bool
    {
        return array_key_exists($key, $this->storage);
    }

    /**
     * implode
     *
     * @param  string  $glue
     *
     * @return  StringObject
     *
     * @since  3.5.1
     */
    public function implode(string $glue): StringObject
    {
        return str(implode($glue, $this->storage));
    }

    /**
     * Create a new iterator from an ArrayObject instance
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->storage);
    }

    /**
     * Returns whether the requested key exists
     *
     * @param  mixed  $key
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return isset($this->storage[$key]);
    }

    /**
     * Returns the value at the specified key
     *
     * @param  mixed  $key
     *
     * @return mixed
     */
    public function &offsetGet($key)
    {
        $ret = null;

        if (!$this->offsetExists($key)) {
            return $ret;
        }

        $ret =& $this->storage[$key];

        return $ret;
    }

    /**
     * Sets the value at the specified key to value
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        if ($key === null) {
            $this->storage[] = $value;

            return;
        }

        $this->storage[$key] = $value;
    }

    /**
     * Unsets the value at the specified key
     *
     * @param  mixed  $key
     *
     * @return void
     */
    public function offsetUnset($key): void
    {
        if ($this->offsetExists($key)) {
            unset($this->storage[$key]);
        }
    }

    /**
     * jsonSerialize
     *
     * @return  array
     *
     * @since  3.5.2
     */
    public function jsonSerialize(): array
    {
        return $this->storage;
    }

    /**
     * mapToArray
     *
     * @param  array  $args
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    protected static function mapUnwrap(array $args): array
    {
        return array_map(fn ($v) => $v instanceof static ? $v->dump() : $v, $args);
    }

    /**
     * wrap
     *
     * @param  mixed  $value
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function wrap($value): self
    {
        if (!$value instanceof static) {
            $value = new static($value);
        }

        return $value;
    }

    /**
     * unwrap
     *
     * @param  mixed  $value
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function unwrap($value)
    {
        if ($value instanceof static) {
            $value = $value->dump();
        }

        return $value;
    }

    /**
     * wrapChildren
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function wrapAll()
    {
        return $this->mapAs(static::class);
    }

    public function as(string $class, ...$args)
    {
        return new $class($this->storage, ...$args);
    }
}
