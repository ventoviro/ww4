<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise\Async;

/**
 * The DeferredAsync class.
 */
class DeferredAsync implements AsyncInterface
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
    public function runAsync(callable $callback): AsyncCursor
    {
        TaskQueue::getInstance()->push($callback);

        return new AsyncCursor(static function () {
            TaskQueue::getInstance()->run();
        });
    }

    /**
     * @inheritDoc
     */
    public function wait(AsyncCursor $cursor): void
    {
        // $cursor->get()();
    }

    /**
     * @inheritDoc
     */
    public function done(AsyncCursor $cursor): void
    {
        $cursor->get()();
    }
}
