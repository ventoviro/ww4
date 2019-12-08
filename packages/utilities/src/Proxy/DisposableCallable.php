<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Proxy;

use function Windwalker\tap;

/**
 * The DisposableCallable class.
 */
class DisposableCallable extends CallableProxy
{
    /**
     * @var bool
     */
    protected $called = false;

    /**
     * @inheritDoc
     */
    public function __invoke(...$args)
    {
        if ($this->called) {
            return;
        }

        return tap(parent::__invoke(...$args), function () {
            $this->called = true;
        });
    }


    /**
     * Method to get property Called
     *
     * @return  bool
     *
     * @since  __DEPLOY_VERSION__
     */
    public function isCalled(): bool
    {
        return $this->called;
    }
}
