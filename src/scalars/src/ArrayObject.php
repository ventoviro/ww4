<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars;

use Windwalker\Scalars\Concern\ArrayAccessTrait;
use Windwalker\Scalars\Concern\ArrayCreationTrait;
use Windwalker\Scalars\Concern\ArrayLoopTrait;
use Windwalker\Scalars\Concern\ArrayModifyTrait;
use Windwalker\Scalars\Concern\ArraySortTrait;
use Windwalker\Utilities\AccessibleTrait;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Classes\MarcoableTrait;
use Windwalker\Utilities\Contract\AccessibleInterface;
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
    use ArrayAccessTrait;

    public const GROUP_TYPE_ARRAY = Arr::GROUP_TYPE_ARRAY;

    public const GROUP_TYPE_KEY_BY = Arr::GROUP_TYPE_KEY_BY;

    public const GROUP_TYPE_MIX = Arr::GROUP_TYPE_MIX;

    /**
     * ArrayObject constructor.
     *
     * @param array $storage
     */
    public function __construct($storage = [])
    {
        $this->storage = TypeCast::toArray($storage);
    }

    /**
     * create
     *
     * @param array $data
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function create($data = [])
    {
        return new static($data);
    }

    /**
     * explode
     *
     * @param string   $delimiter
     * @param string   $string
     * @param int|null $limit
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function explode(string $delimiter, string $string, ?int $limit = null)
    {
        return new static(explode(...array_filter(func_get_args())));
    }

    /**
     * bindNewInstance
     *
     * @param mixed $data
     *
     * @return  static
     */
    protected function newInstance($data = [])
    {
        $new = clone $this;

        $new->storage = $data;

        return $new;
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
    public function with($key, $value)
    {
        $new = clone $this;

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
        $new = clone $this;

        $new->storage[$key] = $new->storage[$key] ?? $default;

        return $new;
    }

    /**
     * withReset
     *
     * @param array $storage
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function withReset(array $storage = [])
    {
        $new = clone $this;

        $new->storage = $storage;

        return $new;
    }

    /**
     * bind
     *
     * @param mixed $data
     * @param array $options
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function bind($data, array $options = [])
    {
        $items = TypeCast::toArray($data);

        foreach ($items as $key => $value) {
            if ($value === null && !($options['replace_nulls'] ?? false)) {
                continue;
            }

            $this->storage[$key] = $value;
        }

        return $this;
    }

    /**
     * keys
     *
     * @param string|int|null $search
     * @param bool|null       $strict
     *
     * @return  static
     *
     * @since  3.5
     */
    public function keys($search = null, ?bool $strict = null)
    {
        return $this->newInstance(array_keys($this->storage, ...array_filter(func_get_args())));
    }

    /**
     * column
     *
     * @param string|int  $name
     * @param string|null $key
     *
     * @return  static
     *
     * @since  3.5
     */
    public function column($name, ?string $key = null)
    {
        return new static(array_column($this->storage, $name, $key));
    }

    /**
     * setColumn
     *
     * @param string|int $name
     * @param mixed      $value
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setColumn($name, $value)
    {
        return $this->newInstance()->apply(function (array $storage) use ($name, $value) {
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
     * @param callable $callback
     * @param array    $args
     *
     * @return  static
     */
    public function apply(callable $callback, ...$args)
    {
        return $this->newInstance($callback(TypeCast::toArray($this), ...$args));
    }

    /**
     * values
     *
     * @return  static
     */
    public function values()
    {
        return $this->newInstance(array_values(TypeCast::toArray($this)));
    }

    /**
     * pipe
     *
     * @param callable $callback
     * @param array    $args
     *
     * @return  static
     */
    public function pipe(callable $callback, ...$args)
    {
        return $callback($this, ...$args);
    }

    /**
     * tap
     *
     * @param callable $callback
     * @param mixed    ...$args
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function tap(callable $callback, ...$args)
    {
        $callback($this, ...$args);

        return $this;
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
     * avg
     *
     * @return  float|int
     *
     * @since  __DEPLOY_VERSION__
     */
    public function avg()
    {
        return $this->sum() / $this->count();
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
    public function unique($sortFlags = SORT_STRING)
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
     * @param array $args
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    protected static function mapUnwrap(array $args): array
    {
        return array_map(static function ($v) {
            return $v instanceof static ? $v->dump() : $v;
        }, $args);
    }

    /**
     * wrap
     *
     * @param mixed $value
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function wrap($value)
    {
        if (!$value instanceof static) {
            $value = new static($value);
        }

        return $value;
    }

    /**
     * unwrap
     *
     * @param mixed $value
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

    public function isEmpty(): bool
    {
        return $this->storage === [];
    }

    public function notEmpty(): bool
    {
        return !$this->isEmpty();
    }
}
