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
 * The NoAsync class.
 */
class NoAsync implements AsyncInterface
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
        $callback();

        return new AsyncCursor();
    }

    /**
     * @inheritDoc
     */
    public function wait(AsyncCursor $cursor): void
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function done(?AsyncCursor $cursor): void
    {
        //
    }
}
