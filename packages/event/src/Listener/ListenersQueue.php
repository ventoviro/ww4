<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Event\Listener;

/**
 * Class ListenerPriorityQueue
 *
 * @since 2.0
 */
class ListenersQueue implements \IteratorAggregate, \Countable
{
    /**
     * @var ListenerItem[]
     */
    protected $queue = [];

    /**
     * Add a listener with the given priority only if not already present.
     *
     * @param  callable  $listener  The listener.
     * @param  integer   $priority  The listener priority.
     * @param  bool      $once      Listener only run once.
     *
     * @return  ListenersQueue  This method is chainable.
     *
     * @since   2.0
     */
    public function add($listener, ?int $priority = null, bool $once = false)
    {
        $this->queue[] = new ListenerItem($listener, $priority, $once);

        return $this;
    }

    /**
     * Remove a listener from the queue.
     *
     * @param  callable  $listener  The listener.
     *
     * @return  ListenersQueue  This method is chainable.
     *
     * @since   2.0
     */
    public function remove(callable $listener)
    {
        $this->queue = array_values(array_filter($this->queue, function (ListenerItem $item) use ($listener) {
            return !$item->is($listener);
        }));

        return $this;
    }

    /**
     * Tell if the listener exists in the queue.
     *
     * @param  callable  $listener  The listener.
     *
     * @return  boolean  True if it exists, false otherwise.
     *
     * @since   2.0
     */
    public function has(callable $listener): bool
    {
        foreach ($this->queue as $item) {
            if ($item->is($listener)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all listeners contained in this queue, sorted according to their priority.
     *
     * @return  callable[]  An array of listeners.
     *
     * @since   2.0
     */
    public function getAll(): array
    {
        $listeners = [];

        foreach ($this as $listener) {
            $listeners[] = $listener;
        }

        return $listeners;
    }

    /**
     * Get the inner queue with its cursor on top of the heap.
     *
     * @return  \SplPriorityQueue  The inner queue.
     *
     * @since   2.0
     */
    public function getIterator()
    {
        // SplPriorityQueue queue is a heap.
        $queue = new \SplPriorityQueue();

        foreach ($this->queue as $item) {
            $queue->insert($item, $item->getPriority());
        }

        return $queue;
    }

    /**
     * Count the number of listeners in the queue.
     *
     * @return  integer  The number of listeners in the queue.
     *
     * @since   2.0
     */
    public function count()
    {
        return count($this->queue);
    }
}
