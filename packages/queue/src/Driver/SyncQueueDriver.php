<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Queue\Driver;

use Windwalker\Queue\Job\JobInterface;
use Windwalker\Queue\QueueMessage;

/**
 * The SyncQueueDriver class.
 *
 * @since  3.2
 */
class SyncQueueDriver implements QueueDriverInterface
{
    /**
     * push
     *
     * @param  QueueMessage  $message
     *
     * @return int|string
     */
    public function push(QueueMessage $message): int|string
    {
        $job = $message->getSerializedJob();
        /** @var JobInterface $job */
        $job = unserialize($job);

        $job->execute();

        return 0;
    }

    /**
     * pop
     *
     * @param  string|null  $queue
     *
     * @return QueueMessage|null
     */
    public function pop(?string $queue = null): ?QueueMessage
    {
        return new QueueMessage();
    }

    /**
     * delete
     *
     * @param  QueueMessage  $message
     *
     * @return SyncQueueDriver
     */
    public function delete(QueueMessage $message)
    {
        return $this;
    }

    /**
     * release
     *
     * @param QueueMessage|string $message
     *
     * @return static
     */
    public function release(QueueMessage $message)
    {
        return $this;
    }
}
