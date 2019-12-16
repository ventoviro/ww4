<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Iterator;

/**
 * The MultiLevelIterator class.
 */
class NestedIterator extends \IteratorIterator
{
    /**
     * @var callable[]
     */
    protected $callbacks = [];

    /**
     * FilesIterator constructor.
     *
     * @param  iterable  $iterator
     */
    public function __construct(iterable $iterator)
    {
        parent::__construct(
            $iterator instanceof \Iterator && !$iterator instanceof \Generator
                ? $iterator
                : (static function () use ($iterator) {
                    foreach ($iterator as $item) {
                        yield $item;
                    }
                })()
        );
    }

    /**
     * wrap
     *
     * @param  callable  $callback
     *
     * @return  static
     */
    public function wrap($callback)
    {
        $this->callbacks[] = $callback;

        return $this;
    }

    /**
     * wrap
     *
     * @param  callable  $callback
     *
     * @return  static
     */
    public function with($callback)
    {
        $new = new static($this->getInnerIterator());

        $new->callbacks = $this->callbacks;
        $new->callbacks[] = $callback;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getInnerIterator(): \Iterator
    {
        $iterator = parent::getInnerIterator();

        foreach ($this->callbacks as $callback) {
            $iterator = (static function () use ($iterator, $callback) {
                return $callback($iterator);
            })();
        }

        return $iterator;
    }

    /**
     * filter
     *
     * @param  callable  $callback
     *
     * @return  static
     */
    public function filter(callable $callback)
    {
        return $this->with(function (iterable $files) use ($callback) {
            foreach ($files as $key => $file) {
                if ($callback($file, $key, $this)) {
                    yield $key => $file;
                }
            }
        });
    }

    /**
     * map
     *
     * @param  callable  $callback
     *
     * @return  static
     */
    public function map(callable $callback)
    {
        return $this->with(function (iterable $files) use ($callback) {
            foreach ($files as $key => $file) {
                yield $key => $callback($file, $key, $this);
            }
        });
    }
}
