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
 * The AbstractQueueDriver class.
 *
 * @since  3.2
 */
interface QueueDriverInterface
{
    /**
     * push
     *
     * @param  QueueMessage  $message
     *
     * @return int|string
     */
    public function push(QueueMessage $message): int|string;

    /**
     * pop
     *
     * @param  string|null  $channel
     *
     * @return QueueMessage|null
     */
    public function pop(?string $channel = null): ?QueueMessage;

    /**
     * delete
     *
     * @param  QueueMessage  $message
     *
     * @return static
     */
    public function delete(QueueMessage $message);

    /**
     * release
     *
     * @param QueueMessage $message
     *
     * @return static
     */
    public function release(QueueMessage $message);
}
