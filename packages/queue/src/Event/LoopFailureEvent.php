<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Queue\Event;

use Windwalker\Event\AbstractEvent;
use Windwalker\Queue\Worker;

/**
 * The WorkerLoopCycleFailure class.
 */
class LoopFailureEvent extends AbstractEvent
{
    protected Worker $worker;
    protected string $message;
    protected \Throwable $exception;

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
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param  string  $message
     *
     * @return  static  Return self to support chaining.
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \Throwable
     */
    public function getException(): \Throwable
    {
        return $this->exception;
    }

    /**
     * @param  \Throwable  $exception
     *
     * @return  static  Return self to support chaining.
     */
    public function setException(\Throwable $exception)
    {
        $this->exception = $exception;

        return $this;
    }
}
