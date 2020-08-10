<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Queue\Failer;

/**
 * The NullQueueFailer class.
 *
 * @since  3.2
 */
class NullQueueFailer implements QueueFailerInterface
{
    /**
     * add
     *
     * @param  string  $connection
     * @param  string  $channel
     * @param  string  $body
     * @param  string  $exception
     *
     * @return  int|string
     */
    public function add(string $connection, string $channel, string $body, string $exception): int|string
    {
        return 0;
    }

    /**
     * all
     *
     * @return  array
     */
    public function all(): array
    {
        return [];
    }

    /**
     * get
     *
     * @param  mixed  $conditions
     *
     * @return array|null
     */
    public function get($conditions): ?array
    {
        return [];
    }

    /**
     * remove
     *
     * @param  mixed  $conditions
     *
     * @return  bool
     */
    public function remove($conditions): bool
    {
        return true;
    }

    /**
     * clear
     *
     * @return  bool
     */
    public function clear(): bool
    {
        return true;
    }
}
