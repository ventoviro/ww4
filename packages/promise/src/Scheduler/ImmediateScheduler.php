<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise\Scheduler;

/**
 * The NoAsync class.
 */
class ImmediateScheduler implements SchedulerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSupported(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function schedule(callable $callback): ScheduleCursor
    {
        $callback();

        return new ScheduleCursor();
    }

    /**
     * @inheritDoc
     */
    public function wait(ScheduleCursor $cursor): void
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function done(?ScheduleCursor $cursor): void
    {
        //
    }
}
