<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars;

use ArrayIterator;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use Windwalker\Scalars\Concern\ArrayContentTrait;
use Windwalker\Scalars\Concern\ArrayCreationTrait;
use Windwalker\Scalars\Concern\ArrayLoopTrait;
use Windwalker\Scalars\Concern\ArrayModifyTrait;
use Windwalker\Scalars\Concern\ArraySortTrait;
use Windwalker\Utilities\AccessibleInterface;
use Windwalker\Utilities\AccessibleTrait;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Classes\MarcoableTrait;
use Windwalker\Utilities\TypeCast;
use function Windwalker\str;

/**
 * The ArrayObject class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArrayObject implements AccessibleInterface
{
    use MarcoableTrait;
    use AccessibleTrait;
    use ArraySortTrait;
    use ArrayCreationTrait;
    use ArrayModifyTrait;
    use ArrayLoopTrait;
    use ArrayContentTrait;

    public const GROUP_TYPE_ARRAY = Arr::GROUP_TYPE_ARRAY;

    public const GROUP_TYPE_KEY_BY = Arr::GROUP_TYPE_KEY_BY;

    public const GROUP_TYPE_MIX = Arr::GROUP_TYPE_MIX;

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
     * Set value and immutable.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function with($key, $value): self
    {
        $new = static::newInstance($this->storage);

        $new->storage[$key] = $value;

        return $new;
    }

    /**
     * withDef
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function withDef($key, $default)
    {
        $new = static::newInstance($this->storage);

        $new->storage[$key] ??= $default;

        return $new;
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
     * @param  array     $args
     *
     * @return  static
     */
    public function apply(callable $callback, ...$args): self
    {
        return static::newInstance($callback(TypeCast::toArray($this), ...$args));
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
     * @param  array     $args
     *
     * @return  static
     */
    public function pipe(callable $callback, ...$args): self
    {
        return $callback($this, ...$args);
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
    public function wrapAll(): self
    {
        return $this->mapAs(static::class);
    }

    public function as(string $class, ...$args)
    {
        return new $class($this->storage, ...$args);
    }

    public function isEmpty(): bool
    {
        return $this->storage === [];
    }

    public function notEmpty(): bool
    {
        return !$this->isEmpty();
    }
}
