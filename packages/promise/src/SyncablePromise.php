<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise;

/**
 * The SyncablePromise class.
 */
class SyncablePromise extends Promise
{
    /**
     * @inheritDoc
     */
    protected function runAsync(callable $callback): void
    {
        $callback();
    }

    /**
     * @inheritDoc
     */
    protected function waitAsync(): void
    {
        //
    }

    /**
     * @inheritDoc
     */
    protected function doneAsync(): void
    {
        //
    }
}
