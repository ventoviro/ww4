<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Iterator;

use Traversable;

/**
 * The MapIterator class.
 */
class MapIterator extends \IteratorIterator
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * MapIterator constructor.
     *
     * @param  Traversable  $iterator
     * @param  callable     $callback
     */
    public function __construct(Traversable $iterator, callable $callback)
    {
        parent::__construct($iterator);

        $this->callback = $callback;
    }

    /**
     * current
     *
     * @return  mixed
     */
    public function current()
    {
        return ($this->callback)(parent::current());
    }
}
