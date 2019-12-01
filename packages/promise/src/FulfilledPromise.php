<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise;

use Windwalker\Utilities\Assert\TypeAssert;

/**
 * The FulfilledPromise class.
 */
class FulfilledPromise implements ExtendedPromiseInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * FulfilledPromise constructor.
     *
     * @param  mixed  $value
     */
    public function __construct($value)
    {
        if ($value instanceof PromiseInterface) {
            new \InvalidArgumentException('You cannot create FulfilledPromise with a Promise.');
        }

        $this->value = $value;
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
        if ($onFulfilled === null) {
            return $this;
        }

        try {
            $onFulfilled($this->value);
        } catch (\Throwable $e) {

        }
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
