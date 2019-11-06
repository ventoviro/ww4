<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars;

use Windwalker\Scalars\Concern\ArrayContentTrait;
use Windwalker\Scalars\Concern\ArrayCreationTrait;
use Windwalker\Scalars\Concern\ArrayLoopTrait;
use Windwalker\Scalars\Concern\ArrayModifyTrait;
use Windwalker\Scalars\Concern\ArraySortTrait;
use Windwalker\Utilities\TypeCast;

/**
 * The ArrayObject class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArrayObject implements \Countable, \ArrayAccess, \IteratorAggregate, \JsonSerializable, ScalarsInterface
{
    use ArraySortTrait;
    use ArrayCreationTrait;
    use ArrayModifyTrait;
    use ArrayLoopTrait;
    use ArrayContentTrait;
    use ScalarsTrait;

    protected array $storage = [];

    /**
     * ArrayObject constructor.
     *
     * @param  array  $storage
     */
    public function __construct($storage = [])
    {
        $this->bind($storage);
    }

    /**
     * bind
     *
     * @param  array|object  $values
     * @param  array         $ignores
     * @param  bool          $replaceNulls
     *
     * @return  $this
     *
     * @since  __DEPLOY_VERSION__
     */
    public function bind($values, array $ignores = [], bool $replaceNulls = false)
    {
        if ($values === null) {
            return $this;
        }

        // Check properties type.
        if (!is_array($values) && !is_object($values)) {
            throw new \InvalidArgumentException(sprintf('Please bind array or object, %s given.', gettype($values)));
        }

        // If is Traversable, get iterator.
        if ($values instanceof \Traversable) {
            $values = iterator_to_array($values);
        } elseif ($values instanceof static) {
            // If is object, convert it to array
            $values = $values->dump();
        } elseif (is_object($values)) {
            // If is object, convert it to array
            $values = get_object_vars($values);
        }

        // Bind the properties.
        foreach ($values as $field => $value) {
            if (in_array($field, $ignores)) {
                continue;
            }

            // Check if the value is null and should be bound.
            if ($value === null && !$replaceNulls) {
                continue;
            }

            // Set the property.
            $this->offsetSet($field, $value);
        }

        return $this;
    }

    /**
     * bindNewInstance
     *
     * @param mixed $data
     *
     * @return  static
     */
    protected static function newInstance($data = [])
    {
        return new static($data);
    }

    /**
     * keys
     *
     * @param string|null $search
     * @param bool|null   $strict
     *
     * @return  static
     *
     * @since  3.5
     */
    public function keys(?string $search = null, ?bool $strict = null)
    {
        if (func_get_args()['search'] ?? false) {
            return array_keys($this->storage, $search, (bool) $strict);
        }

        return new static(array_keys($this->storage));
    }

    /**
     * column
     *
     * @param string      $name
     * @param string|null $key
     *
     * @return  static
     *
     * @since  3.5
     */
    public function column(string $name, ?string $key = null): self
    {
        return new static(array_column($this->storage, $name, $key));
    }

    /**
     * apply
     *
     * @param callable $callback
     *
     * @return  static
     */
    public function apply(callable $callback)
    {
        return static::newInstance($callback(TypeCast::toArray($this)));
    }

    /**
     * values
     *
     * @return  static
     */
    public function values()
    {
        return static::newInstance(array_values(TypeCast::toArray($this)));
    }

    /**
     * pipe
     *
     * @param callable $callback
     *
     * @return  static
     */
    public function pipe(callable $callback)
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
     * @throws \InvalidArgumentException
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
     * @return array
     */
    public function dump(bool $recursive = false): array
    {
        if (!$recursive) {
            return $this->storage;
        }

        return TypeCast::toArray($this->storage, true);
    }

    /**
     * search
     *
     * @param mixed $value
     * @param bool  $strict
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
     * @param mixed $value
     * @param bool  $strict
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
     * @param int $sortFlags
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
     * @param mixed $value
     * @param bool  $strict
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
     * @param mixed $key
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
     * @param string $glue
     *
     * @return  string
     *
     * @since  3.5.1
     */
    public function implode(string $glue): string
    {
        return implode($glue, $this->storage);
    }

    /**
     * Create a new iterator from an ArrayObject instance
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->storage);
    }

    /**
     * Returns whether the requested key exists
     *
     * @param  mixed  $key
     *
     * @return bool
     */
    public function offsetExists($key)
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
    public function offsetSet($key, $value)
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
    public function offsetUnset($key)
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
    public function jsonSerialize()
    {
        return $this->storage;
    }

    public function toNumber(): NumberObject
    {
        throw new \TypeError('Collection cannot convert to number.');
    }

    public function toString(): StringObject
    {
        throw new \TypeError('Collection cannot convert to string.');
    }

    public function toArray(): ArrayObject
    {
        return clone $this;
    }
}
