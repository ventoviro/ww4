<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\Promise;

use ReflectionException;

/**
 * resolve
 *
 * @param  mixed|PromiseInterface  $promiseOrValue
 *
 * @return  FulfilledPromise|Promise
 *
 * @throws ReflectionException
 */
function resolve($promiseOrValue = null)
{
    if ($promiseOrValue instanceof ExtendedPromiseInterface) {
        return $promiseOrValue;
    }

    if (is_thenable($promiseOrValue)) {
        return new Promise(static function ($resolve, $reject) use ($promiseOrValue) {
            $promiseOrValue->then($resolve, $reject);
        });
    }

    return new FulfilledPromise($promiseOrValue);
}

/**
 * reject
 *
 * @param  mixed|PromiseInterface  $promiseOrValue
 *
 * @return  ExtendedPromiseInterface
 */
function reject($promiseOrValue = null)
{
    if ($promiseOrValue instanceof PromiseInterface) {
        return new RejectedPromise(static function ($resolve, $reject) use ($promiseOrValue) {
            return $promiseOrValue->then($resolve, $reject);
        });
    }

    return new RejectedPromise($promiseOrValue);
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
 * nope
 *
 * @return  \Closure
 */
function nope(): \Closure
{
    return static function () {};
}
