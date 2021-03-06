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
 * Interface ExtendedPromiseInterface
 *
 * @since  __DEPLOY_VERSION__
 */
interface ExtendedPromiseInterface extends PromiseInterface
{
    /**
     * @param  callable|null  $onFulfilled
     *
     * @return static
     */
    public function done(?callable $onFulfilled = null);

    /**
     * @param  callable  $onRejected
     *
     * @return static
     */
    public function catch(?callable $onRejected);

    /**
     * @param  callable  $onFulfilledOrRejected
     *
     * @return static
     */
    public function finally(?callable $onFulfilledOrRejected);
}
