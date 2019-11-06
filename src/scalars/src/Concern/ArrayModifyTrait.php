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
        return static::newInstance(array_pad($this->storage, $size, $value));
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

        return static::newInstance($new);
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

        return static::newInstance($new);
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
        return static::newInstance($this->storage)->splice(0, -$num);
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
        return static::newInstance($this->storage)->splice($num);
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
        $args = array_map([Arr::class, 'toArray'], $args);

        return static::newInstance(array_replace($this->storage, ...$args));
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
        $args = TypeCast::mapAs($args, 'array');

        return static::newInstance(array_replace_recursive($this->storage, ...$args));
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
        return static::newInstance(array_reverse($this->storage, $preserveKeys));
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
        return static::newInstance(array_slice($this->storage, ...func_get_args()));
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
        return static::newInstance(array_splice($this->storage, ...func_get_args()));
    }

    /**
     * insertAfter
     *
     * @param int   $key
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function insertAfter(int $key, $value): self
    {
        return static::newInstance($this->storage)->splice($key + 1, 0, $value);
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
        return static::newInstance($this->storage)->splice($key, 0, $value);
    }

    /**
     * only
     *
     * @param array|string $fields
     *
     *
     * @return  static
     */
    public function only($fields)
    {
        $fields = (array) $fields;

        $new = static::newInstance();

        foreach ($fields as $origin => $field) {
            if (is_numeric($origin)) {
                $new[$field] = $this[$field];
            } else {
                $new[$field] = $this[$origin];
            }
        }

        return $new;
    }

    /**
     * except
     *
     * @param array|string $fields
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function except($fields)
    {
        $fields = (array) $fields;

        $new = static::newInstance();

        foreach ($fields as $origin => $field) {
            unset($new[$field]);
        }

        return $new;
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

        return static::newInstance($new);
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
        return Arr::takeout($this, $key, $default, $delimiter);
    }

    /**
     * chunk
     *
     * @param int  $size
     * @param bool $preserveKeys
     *
     * @return  static
     */
    public function chunk($size, $preserveKeys = null)
    {
        return static::newInstance(
            array_map(
                [$this, 'bindNewInstance'],
                array_chunk(TypeCast::toArray($this), ...func_get_args())
            )
        );
    }

    /**
     * groupBy
     *
     * @param string $column
     *
     * @return  static
     *
     * @since  3.5.3
     */
    public function groupBy(string $column)
    {
        return static::newInstance(Arr::group($this->dump(), $column, true));
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
        return static::newInstance(Arr::group($this->dump(), $field));
    }
}
