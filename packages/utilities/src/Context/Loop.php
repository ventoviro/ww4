<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Context;

/**
 * The LoopContext class.
 *
 * @since  __DEPLOY_VERSION__
 */
class Loop
{
    /**
     * @var int
     */
    protected $index;

    /**
     * @var mixed
     */
    protected $key;

    /**
     * @var  int
     */
    protected $length;

    /**
     * @var  Loop|null
     */
    protected $parent;

    /**
     * @var  bool
     */
    protected $stop = false;

    /**
     * @var mixed
     */
    protected $target;

    /**
     * LoopContext constructor.
     *
     * @param  int        $length
     * @param  mixed      $target
     * @param  Loop|null  $parent
     */
    public function __construct(int $length, &$target, ?Loop $parent = null)
    {
        $this->length = $length;
        $this->parent = $parent;
        $this->target = $target;
    }

    /**
     * loop
     *
     * @param  int    $index
     * @param  mixed  $key
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function loop(int $index, $key)
    {
        $this->index = $index;
        $this->key   = $key;

        return $this;
    }

    public function index(): int
    {
        return $this->index;
    }

    /**
     * Method to get property Index
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function key()
    {
        return $this->key;
    }

    public function length(): int
    {
        return $this->length;
    }

    public function parent(): ?Loop
    {
        return $this->parent;
    }

    public function stop(int $depth = 1): bool
    {
        $this->stop = true;

        if ($depth > 1 && $this->parent) {
            $this->parent->stop($depth - 1);
        }

        return true;
    }

    public function isStop(): bool
    {
        return $this->stop;
    }

    public function first()
    {
        return $this->target[array_key_first($this->target)];
    }

    public function last()
    {
        return $this->target[array_key_last($this->target)];
    }

    public function firstKey()
    {
        return $this->target[array_key_first($this->target)];
    }

    public function lastKey()
    {
        return $this->target[array_key_last($this->target)];
    }
}
