<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise;

use React\Promise\RejectedPromise;
use function React\Promise\resolve;

/**
 * The Promise class.
 *
 * @since  __DEPLOY_VERSION__
 */
class Promise implements ExtendedPromiseInterface
{
    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var callable
     */
    protected $handler;

    /**
     * Promise constructor.
     *
     * @param  callable  $resolver
     *
     * @throws \ReflectionException
     */
    public function __construct(callable $resolver)
    {
        // Explicitly overwrite arguments with null values before invoking
        // resolver function. This ensure that these arguments do not show up
        // in the stack trace in PHP 7+ only.
        $cb = $resolver;
        $this->call($cb);
    }

    /**
     * @inheritDoc
     */
    public function done(callable $onFulfilled = null, callable $onRejected = null)
    {
    }

    /**
     * @inheritDoc
     */
    public function catch(callable $onRejected)
    {
    }

    /**
     * @inheritDoc
     */
    public function always(callable $onFulfilledOrRejected)
    {
    }

    /**
     * @inheritDoc
     *
     * @throws \ReflectionException
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        if (null !== $this->result) {
            return $this->result->then($onFulfilled, $onRejected);
        }

        $promise = new static(function (callable $resolve, callable $reject) use (&$promise) {
            $promise->handler = function () {

            };
        });

        return $promise;
    }

    /**
     * @inheritDoc
     */
    public function getState(): string
    {
    }

    /**
     * @inheritDoc
     */
    public function resolve($value)
    {
        $this->settle(resolve($value));
    }

    /**
     * @inheritDoc
     */
    public function reject($reason)
    {
    }

    /**
     * @inheritDoc
     */
    public function wait($unwrap = true)
    {
    }

    private function createResolver(callable $onFulfilled = null, callable $onRejected = null)
    {
        return function (callable $resolve, callable $reject) {
            $this->handlers[] = function (PromiseInterface $promise) {
                $promise->resolve()
            };
        };
    }

    private function settle(ExtendedPromiseInterface $promise)
    {
        if ($promise === $this) {
            $promise = new RejectedPromise(
                new \LogicException('Cannot resolve a promise with itself.')
            );
        }

        $handlers = $this->handlers;

        $this->result = $promise;

        foreach ($handlers as $handler) {
            $handler($promise);
        }
    }

    /**
     * Calling callback.
     *
     * This method is part of Reactphp/Promise
     *
     * @see https://github.com/reactphp/promise
     *
     * @param  callable  $cb
     *
     * @return  void
     *
     * @throws \ReflectionException
     */
    private function call(callable $cb)
    {
        // Explicitly overwrite argument with null value. This ensure that this
        // argument does not show up in the stack trace in PHP 7+ only.
        $callback = $cb;
        $cb = null;

        // Use reflection to inspect number of arguments expected by this callback.
        // We did some careful benchmarking here: Using reflection to avoid unneeded
        // function arguments is actually faster than blindly passing them.
        // Also, this helps avoiding unnecessary function arguments in the call stack
        // if the callback creates an Exception (creating garbage cycles).
        if (\is_array($callback)) {
            $ref = new \ReflectionMethod($callback[0], $callback[1]);
        } elseif (\is_object($callback) && !$callback instanceof \Closure) {
            $ref = new \ReflectionMethod($callback, '__invoke');
        } else {
            $ref = new \ReflectionFunction($callback);
        }
        $args = $ref->getNumberOfParameters();

        try {
            if ($args === 0) {
                $callback();
            } else {
                // Keep references to this promise instance for the static resolve/reject functions.
                // By using static callbacks that are not bound to this instance
                // and passing the target promise instance by reference, we can
                // still execute its resolving logic and still clear this
                // reference when settling the promise. This helps avoiding
                // garbage cycles if any callback creates an Exception.
                // These assumptions are covered by the test suite, so if you ever feel like
                // refactoring this, go ahead, any alternative suggestions are welcome!
                $target =& $this;

                $callback(
                    static function ($value = null) use (&$target) {
                        if ($target !== null) {
                            $target->settle(resolve($value));
                            $target = null;
                        }
                    },
                    static function ($reason = null) use (&$target) {
                        if ($target !== null) {
                            $target->reject($reason);
                            $target = null;
                        }
                    }
                );
            }
        } catch (\Throwable $e) {
            $target = null;
            $this->reject($e);
        }
    }
}
