<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\Promise;

/**
 * resolve
 *
 * @param  mixed|PromiseInterface  $promiseOrValue
 *
 * @return  ExtendedPromiseInterface
 */
function resolve($promiseOrValue = null): ExtendedPromiseInterface
{
    return Promise::resolved($promiseOrValue);
}

/**
 * reject
 *
 * @param  mixed|PromiseInterface  $promiseOrValue
 *
 * @return  ExtendedPromiseInterface
 */
function reject($promiseOrValue = null): ExtendedPromiseInterface
{
    return Promise::rejected($promiseOrValue);
}

/**
 * is_thenable
 *
 * @param mixed $value
 *
 * @return  bool
 */
function is_thenable($value): bool
{
    return \is_object($value) && \method_exists($value, 'then');
}

/**
 * async
 *
 * @param  callable  $callable
 *
 * @return  \Closure
 */
function asyncable(callable $callable): \Closure
{
    return static function (...$args) use ($callable): Promise {
        return new Promise(static function ($resolve, $reject) use ($callable, $args) {
            try {
                $resolve($callable(...$args));
            } catch (\Throwable $e) {
                $reject($e);
            }
        });
    };
}

/**
 * async
 *
 * @param  callable  $callable
 *
 * @return  Promise
 */
function async(callable $callable): Promise
{
    return new Promise(static function ($resolve, $reject) use ($callable) {
        try {
            $resolve($callable());
        } catch (\Throwable $e) {
            $reject($e);
        }
    });
}

/**
 * await
 *
 * @param  PromiseInterface  $promise
 *
 * @return  mixed
 */
function await(PromiseInterface $promise)
{
    return $promise->wait();
}

/**
 * nope
 *
 * @return  \Closure
 */
function nope(): \Closure
{
    return static function () {
        //
    };
}
