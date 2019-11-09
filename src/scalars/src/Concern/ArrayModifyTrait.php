<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars\Concern;

use Windwalker\Data\Traits\CollectionTrait;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\TypeCast;

/**
 * Trait ArrayModifyTrait
 *
 * @since  __DEPLOY_VERSION__
 */
trait ArrayModifyTrait
{
    /**
     * pad
     *
     * @param int   $size
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function pad(int $size, $value): self
    {
        return $this->newInstance(array_pad($this->storage, $size, $value));
    }

    /**
     * leftPad
     *
     * @param int   $size
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function leftPad(int $size, $value): self
    {
        return $this->pad(-$size, $value);
    }

    /**
     * leftPad
     *
     * @param int   $size
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function rightPad(int $size, $value): self
    {
        return $this->pad($size, $value);
    }

    /**
     * pop
     *
     * @return  mixed
     *
     * @since  3.5
     */
    public function pop()
    {
        return array_pop($this->storage);
    }

    /**
     * shift
     *
     * @return  mixed
     *
     * @since  3.5
     */
    public function shift()
    {
        return array_shift($this->storage);
    }

    /**
     * push
     *
     * @param mixed ...$value
     *
     * @return  int
     *
     * @since  3.5
     */
    public function push(...$value): int
    {
        return array_push($this->storage, ...$value);
    }

    /**
     * unshift
     *
     * @param mixed ...$value
     *
     * @return  int
     *
     * @since  3.5
     */
    public function unshift(...$value): int
    {
        return array_unshift($this->storage, ...$value);
    }

    /**
     * concat
     *
     * @param  mixed  ...$args
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function append(...$args): self
    {
        $new = $this->storage;
        array_push($new, ...$args);

        return $this->newInstance($new);
    }

    /**
     * concatStart
     *
     * @param  mixed  ...$args
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function prepend(...$args): self
    {
        $new = $this->storage;
        array_unshift($new, ...$args);

        return $this->newInstance($new);
    }

    /**
     * removeEnd
     *
     * @param  int  $num
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function removeLast($num = 1): self
    {
        return (clone $this)->splice(0, -$num);
    }

    /**
     * removeStart
     *
     * @param  int  $num
     *
     * @return  $this
     *
     * @since  3.5.13
     */
    public function removeFirst($num = 1): self
    {
        return (clone $this)->splice($num);
    }

    /**
     * replace
     *
     * @param array[]|static[] ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function replace(...$args): self
    {
        return $this->newInstance(array_replace($this->storage, ...static::mapUnwrap($args)));
    }

    /**
     * replaceRecursive
     *
     * @param array[]|static[] ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function replaceRecursive(...$args): self
    {
        return $this->newInstance(array_replace_recursive($this->storage, ...static::mapUnwrap($args)));
    }

    /**
     * reverse
     *
     * @param bool $preserveKeys
     *
     * @return  static
     *
     * @since  3.5
     */
    public function reverse(bool $preserveKeys = false): self
    {
        return $this->newInstance(array_reverse($this->storage, $preserveKeys));
    }

    /**
     * slice
     *
     * @param int      $offset
     * @param int|null $length
     * @param bool     $preserveKeys
     *
     * @return  static
     *
     * @since  3.5
     */
    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self
    {
        return $this->newInstance(array_slice($this->storage, ...func_get_args()));
    }

    /**
     * slice
     *
     * @param int      $offset
     * @param int|null $length
     * @param mixed    $replacement
     *
     * @return  static
     *
     * @since  3.5
     */
    public function splice(int $offset, ?int $length = null, $replacement = null): self
    {
        return $this->newInstance(array_splice($this->storage, ...func_get_args()));
    }

    /**
     * insertAfter
     *
     * @param  int    $key
     * @param  array  $args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function insertAfter(int $key, ...$args): self
    {
        $new = clone $this;

        $new->splice($key + 1, 0, $args);

        return $new;
    }

    /**
     * insertBefore
     *
     * @param int   $key
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function insertBefore(int $key, $value): self
    {
        $new = clone $this;

        $new->splice($key, 0, $value);

        return $new;
    }

    /**
     * only
     *
     * @param array $fields
     *
     *
     * @return  static
     */
    public function only(array $fields)
    {
        return $this->newInstance(Arr::only($this->storage, $fields));
    }

    /**
     * except
     *
     * @param array $fields
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function except(array $fields)
    {
        return $this->newInstance(Arr::except($this->storage, $fields));
    }

    /**
     * shuffle
     *
     * @return  static
     *
     * @since  3.5
     */
    public function shuffle(): self
    {
        $new = $this->storage;

        shuffle($new);

        return $this->newInstance($new);
    }

    /**
     * takeout
     *
     * @param string $key
     * @param mixed  $default
     * @param string $delimiter
     *
     * @return  mixed
     */
    public function takeout($key, $default = null, $delimiter = '.')
    {
        return Arr::takeout($this->storage, $key, $default, $delimiter);
    }

    /**
     * chunk
     *
     * @param int  $size
     * @param bool $preserveKeys
     *
     * @return  static
     */
    public function chunk(int $size, bool $preserveKeys = false)
    {
        return $this->newInstance(array_chunk($this->storage, $size, $preserveKeys))
            ->wrapAll();
    }

    /**
     * groupBy
     *
     * @param  string  $column
     * @param  int     $type
     *
     * @return  static
     *
     * @since  3.5.3
     */
    public function groupBy(string $column, int $type = Arr::GROUP_TYPE_ARRAY)
    {
        return $this->newInstance(Arr::group($this->dump(), $column, $type));
    }

    /**
     * keyBy
     *
     * @param string $field
     *
     * @return  static
     */
    public function keyBy(string $field)
    {
        return $this->newInstance(Arr::group($this->dump(), $field, Arr::GROUP_TYPE_KEY_BY));
    }
}
