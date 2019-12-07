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
 * Interface AsyncInterface
 */
interface AsyncInterface
{
    /**
     * isSupported
     *
     * @return  bool
     */
    public static function isSupported(): bool;

    /**
     * runAsync
     *
     * @param  callable  $callback
     *
     * @return  AsyncCursor
     */
    public function runAsync(callable $callback): AsyncCursor;

    /**
     * wait
     *
     * @param  AsyncCursor  $cursor
     *
     * @return  void
     */
    public function wait(AsyncCursor $cursor): void;

    /**
     * done
     *
     * @param  AsyncCursor  $cursor
     *
     * @return  void
     */
    public function done(?AsyncCursor $cursor): void;
}
