<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Proxy;

/**
 * The CallbackProxy class.
 */
class CallableProxy
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * CallbackProxy constructor.
     *
     * @param  callable  $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * __invoke
     *
     * @param  mixed  ...$args
     *
     * @return  mixed
     */
    public function __invoke(...$args)
    {
        $callback = $this->callable;

        return $callback(...$args);
    }

    /**
     * Method to get property Callable
     *
     * @return  callable
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }
}
