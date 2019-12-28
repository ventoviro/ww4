<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Reactor;

use Rx\Disposable\CallbackDisposable;
use Rx\Scheduler\EventLoopScheduler;
use Swoole\Timer;

/**
 * The SwooleRxLooper class.
 */
final class SwooleRxScheduler
{
    /**
     * createLoop
     *
     * @return  callable
     */
    public static function createLoop(): callable
    {
        return static function ($ms, $callable) {
            $timer = Timer::after($ms + 1, $callable);

            return new CallbackDisposable(
                function () use ($timer) {
                    Timer::clear($timer);
                }
            );
        };
    }

    /**
     * createScheduler
     *
     * @return  EventLoopScheduler
     */
    public static function createScheduler(): EventLoopScheduler
    {
        return new EventLoopScheduler(static::createLoop());
    }

    /**
     * createSchedulerFactory
     *
     * @return  callable
     */
    public static function factory(): callable
    {
        return static function () {
            return static::createScheduler();
        };
    }
}
