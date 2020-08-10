<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Queue\Driver;

use Windwalker\Queue\QueueMessage;

/**
 * The NullQueueDriver class.
 *
 * @since  3.2
 */
class NullQueueDriver implements QueueDriverInterface
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
        return 0;
    }

    /**
     * pop
     *
     * @param  string|null  $channel
     *
     * @return QueueMessage|null
     */
    public function pop(?string $channel = null): ?QueueMessage
    {
        return null;
    }

    /**
     * delete
     *
     * @param  QueueMessage  $message
     *
     * @return static
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
