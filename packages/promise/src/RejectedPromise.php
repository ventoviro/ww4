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
 * The RejectedPromise class.
 */
class RejectedPromise implements ExtendedPromiseInterface
{
    private $reason;

    public function __construct($reason = null)
    {
        if ($reason instanceof PromiseInterface) {
            throw new \InvalidArgumentException('You cannot create RejectedPromise with a promise.');
        }

        $this->reason = $reason;
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
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
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
}
