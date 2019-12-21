<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Iterator;

use Windwalker\Filesystem\FileObject;

/**
 * The MultiLevelIterator class.
 */
class NestedIterator implements \OuterIterator
{
    /**
     * @var \Traversable
     */
    protected $innerIterator;

    /**
     * @var \Generator
     */
    protected $compiledIterator;

    /**
     * @var callable[]
     */
    protected $callbacks = [];

    /**
     * FilesIterator constructor.
     *
     * @param  iterable|callable  $iterator
     */
    public function __construct($iterator)
    {
        if (is_callable($iterator)) {
            $iterator = new RewindableGenerator($iterator);
        }

        $this->innerIterator = $iterator instanceof \Traversable
            ? $iterator
            : new \ArrayIterator($iterator);
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
        $new = $this->cloneNew();

        $new->callbacks = $this->callbacks;
        $new->callbacks[] = $callback;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getInnerIterator(): \Iterator
    {
        return  $this->innerIterator;
    }

    /**
     * compileIterator
     *
     * @param  bool  $refresh
     *
     * @return  \Traversable
     */
    protected function compileIterator($refresh = false): \Traversable
    {
        if ($this->compiledIterator === null || $refresh) {
            if ($this->checkRewindable($this->innerIterator)) {
                $this->innerIterator->rewind();
            }

            $iterator = $this->innerIterator;

            foreach ($this->callbacks as $callback) {
                $iterator = (static function () use ($iterator, $callback) {
                    return $callback($iterator);
                })();
            }

            $this->compiledIterator = $iterator;
        }

        return $this->compiledIterator;
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

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->compileIterator()->current();
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->compileIterator()->next();
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->compileIterator()->key();
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->compileIterator()->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->compileIterator(true);
    }

    /**
     * This iterator unable to use native clone. We clone it manually.
     *
     * @return  static
     */
    protected function cloneNew()
    {
        return new static($this->getInnerIterator());
    }

    /**
     * Method to set property innerIterator
     *
     * @param  \Traversable  $innerIterator
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setInnerIterator(\Traversable $innerIterator)
    {
        $this->innerIterator = $innerIterator;

        return $this;
    }

    /**
     * checkRewindable
     *
     * @param  \Traversable  $iter
     *
     * @return  bool
     */
    protected function checkRewindable(\Traversable $iter): bool
    {
        if ($iter instanceof \Generator) {
            return false;
        }

        if ($iter instanceof \OuterIterator) {
            return $this->checkRewindable($iter->getInnerIterator());
        }

        return $iter instanceof \Iterator;
    }
}
