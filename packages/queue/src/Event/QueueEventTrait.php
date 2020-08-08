<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Queue\Event;

use Windwalker\Queue\QueueAdapter;
use Windwalker\Queue\Worker;

/**
 * Trait QueueEventTrait
 */
trait QueueEventTrait
{
    protected Worker $worker;
    protected QueueAdapter $adapter;

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
     * @return QueueAdapter
     */
    public function getAdapter(): QueueAdapter
    {
        return $this->adapter;
    }

    /**
     * @param  QueueAdapter  $adapter
     *
     * @return  static  Return self to support chaining.
     */
    public function setAdapter(QueueAdapter $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}
