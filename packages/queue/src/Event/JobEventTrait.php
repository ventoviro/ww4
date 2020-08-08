<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Queue\Event;

use Windwalker\Queue\Job\JobInterface;
use Windwalker\Queue\QueueMessage;

/**
 * The JobEventTrait class.
 */
trait JobEventTrait
{
    use QueueEventTrait;

    protected QueueMessage $message;
    protected JobInterface $job;

    /**
     * @return QueueMessage
     */
    public function getMessage(): QueueMessage
    {
        return $this->message;
    }

    /**
     * @param  QueueMessage  $message
     *
     * @return  static  Return self to support chaining.
     */
    public function setMessage(QueueMessage $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return JobInterface
     */
    public function getJob(): JobInterface
    {
        return $this->job;
    }

    /**
     * @param  JobInterface  $job
     *
     * @return  static  Return self to support chaining.
     */
    public function setJob(JobInterface $job)
    {
        $this->job = $job;

        return $this;
    }
}
