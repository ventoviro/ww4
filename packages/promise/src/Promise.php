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
 * The Promise class.
 *
 * @since  __DEPLOY_VERSION__
 */
class Promise implements ExtendedPromiseInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var callable[]
     */
    protected $children = [];

    /**
     * @var string
     */
    protected $state = self::PENDING;

    /**
     * create
     *
     * @param  callable  $resolver
     *
     * @return  static
     */
    public static function create(?callable $resolver = null)
    {
        return new static($resolver);
    }

    /**
     * Promise constructor.
     *
     * @param  callable  $resolver
     */
    public function __construct(?callable $resolver = null)
    {
        // Explicitly overwrite arguments with null values before invoking
        // resolver function. This ensure that these arguments do not show up
        // in the stack trace in PHP 7+ only.
        $cb = $resolver;

        // Todo: Run call in async process
        if ($resolver) {
            $this->call($cb);
        }
    }

    /**
     * @inheritDoc
     */
    public function done(callable $onFulfilled = null, callable $onRejected = null)
    {
        return $this->then($onFulfilled);
    }

    /**
     * @inheritDoc
     */
    public function catch(callable $onRejected)
    {
        return $this->then(null, $onRejected);
    }

    /**
     * @inheritDoc
     */
    public function finally(callable $onFulfilledOrRejected)
    {
        return $this->then(
            function () use ($onFulfilledOrRejected) {
                $onFulfilledOrRejected();

                return $this->value;
            },
            function () use ($onFulfilledOrRejected) {
                $onFulfilledOrRejected();

                return static::rejected($this->value);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function then($onFulfilled = null, $onRejected = null)
    {
        if ($this->getState() === static::PENDING) {
            $child = new static(nope());

            $this->children[] = [
                $child,
                is_callable($onFulfilled) ? $onFulfilled : null,
                is_callable($onRejected) ? $onRejected : null
            ];

            return $child;
        }

        $handler = $this->getState() === static::FULFILLED
            ? $onFulfilled
            : $onRejected;

        // If onFulfilled or onRejected is not function, return promise with same value and state.
        // @see https://promisesaplus.com/#point-40
        if (!is_callable($handler)) {
            if ($this->getState() === static::FULFILLED) {
                return static::resolved($this->value);
            }

            return static::rejected($this->value);
        }

        try {
            $value = $handler($this->value);

            $child = $this->resolveProcess($value);

            return $child;
        } catch (\Throwable $e) {
            return static::rejected($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * resolved
     *
     * @param mixed $value
     *
     * @return  ExtendedPromiseInterface
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function resolved($value): ExtendedPromiseInterface
    {
        $promise = null;

        if ($value instanceof ExtendedPromiseInterface) {
            $promise = $value;
        }

        if (is_thenable($value)) {
            $promise = new Promise(static function ($resolve, $reject) use ($value) {
                $value->then($resolve, $reject);
            });
        }

        if ($promise === null) {
            $promise = new Promise(nope());
            $promise->value = $value;
        }

        $promise->state = static::FULFILLED;

        return $promise;
    }

    /**
     * rejected
     *
     * @param mixed $value
     *
     * @return  ExtendedPromiseInterface
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function rejected($value): ExtendedPromiseInterface
    {
        $promise = null;

        if ($value instanceof ExtendedPromiseInterface) {
            $promise = $value;
        }

        if (is_thenable($value)) {
            $promise = new Promise(static function ($resolve, $reject) use ($value) {
                $value->then($resolve, $reject);
            });
        }

        if ($promise === null) {
            $promise = new Promise(nope());
            $promise->value = $value;
        }

        $promise->state = static::REJECTED;

        return $promise;
    }

    /**
     * @inheritDoc
     */
    public function resolve($value)
    {
        if ($value === $this) {
            throw new \TypeError('Unable to resolve self.');
        }

        if ($this->getState() !== static::PENDING) {
            return;
        }

        // If value is promise, start resolving after it resolved.
        if ($value instanceof PromiseInterface || is_thenable($value)) {
            $value->then(
                function ($x = null) {
                    $this->settle(static::FULFILLED, $x);
                    return $x;
                },
                function ($r = null) {
                    $this->settle(static::FULFILLED, $r);
                    return static::rejected($r);
                }
            );

            return;
        }

        $this->settle(static::FULFILLED, $value);
    }

    /**
     * @inheritDoc
     */
    public function reject($reason)
    {
        if ($reason === $this) {
            throw new \TypeError('Unable to resolve self.');
        }

        if ($this->getState() !== static::PENDING) {
            return;
        }

        // If value is promise, start rejecting after it rejected.
        if ($reason instanceof PromiseInterface || is_thenable($reason)) {
            $reason->then(
                function ($x = null) {
                    $this->settle(static::REJECTED, $x);
                    return $x;
                },
                function ($r = null) {
                    $this->settle(static::REJECTED, $r);
                    return static::rejected($r);
                }
            );

            return;
        }

        $this->settle(static::REJECTED, $reason);
    }

    /**
     * @inheritDoc
     *
     * @throws \Throwable
     */
    public function wait()
    {
        if ($this->getState() === static::PENDING) {
            throw new \LogicException('Cannot wait before fulfilled or rejected.');
        }

        if ($this->value instanceof \Throwable && $this->getState() === static::REJECTED) {
            throw $this->value;
        }

        return $this->value;
    }

    /**
     * resolveProcess
     *
     * @see @see https://promisesaplus.com/#point-48
     *
     * @param $x
     *
     * @return  PromiseInterface
     */
    private function resolveProcess($x): PromiseInterface
    {
        // This method always return new Promise so it's unnecessary to check x is not self.

        // 2.3.2 If x is a promise, adopt its state
        if ($x instanceof PromiseInterface) {
            return $x->then();
        }

        if (is_thenable($x)) {
            // 2.3.3.3 If then is a function, call it with x as this, first argument resolvePromise,
            // and second argument rejectPromise...
            return new Promise(static function ($resolve, $reject) use ($x) {
                $x->then($resolve, $reject);
            });
        }

        return static::resolved($x);
    }

    /**
     * settle
     *
     * @param  string  $state
     * @param  mixed   $value
     *
     * @return  void
     *
     * @since  __DEPLOY_VERSION__
     */
    private function settle(string $state, $value): void
    {
        if ($value instanceof PromiseInterface) {
            throw new \LogicException('Value at here should not be Promise.');
        }

        $handlers = $this->children;

        $this->state = $state;
        $this->value = $value;

        foreach ($handlers as $handler) {
            /** @var PromiseInterface $promise */
            [$promise, $onFulfilled, $onRejected] = $handler;

            $handler = $this->getState() === static::FULFILLED
                ? $onFulfilled
                : $onRejected;

            // If onFulfilled or onRejected is not function, return promise with same value and state.
            // @see https://promisesaplus.com/#point-40
            if (!is_callable($handler)) {
                if ($state === static::FULFILLED) {
                    $promise->resolve($value);
                }

                $promise->reject($value);
            }

            try {
                $value = $handler($value);
            } catch (\Throwable $e) {
                $promise->reject($e);
                continue;
            }

            $promise->resolve($value);
        }
    }

    /**
     * Calling callback.
     *
     * This method is part of Reactphp/Promise
     *
     * @see https://github.com/reactphp/promise
     *
     * @param  callable|null  $cb
     *
     * @return  void
     *
     * @throws
     */
    private function call(callable $cb): void
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
                            $target->resolve($value);
                            $target = null;
                        }

                        return $value;
                    },
                    static function ($reason = null) use (&$target) {
                        if ($target !== null) {
                            $target->reject($reason);
                            $target = null;
                        }

                        return $reason;
                    }
                );
            }
        } catch (\Throwable $e) {
            $target = null;
            $this->reject($e);
        }
    }
}
