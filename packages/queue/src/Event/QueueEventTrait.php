<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Queue\Event;

use Windwalker\Queue\Queue;
use Windwalker\Queue\Worker;

/**
 * Trait QueueEventTrait
 */
trait QueueEventTrait
{
    protected Worker $worker;
    protected Queue $queue;

    /**
     * @return Worker
     */
    public function getWorker(): Worker
    {
        return $this->worker;
    }

    /**
     * @param  Worker  $worker
     *
     * @return  static  Return self to support chaining.
     */
    public function setWorker(Worker $worker)
    {
        $this->worker = $worker;

        return $this;
    }

    /**
     * @return Queue
     */
    public function getQueue(): Queue
    {
        return $this->queue;
    }

    /**
     * @param  Queue  $queue
     *
     * @return  static  Return self to support chaining.
     */
    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;

        return $this;
    }
}
