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
 * The ChainCallable class.
 */
class ChainableCallable extends CallableProxy
{
    /**
     * @var callable[]
     */
    protected $queue = [];

    /**
     * @inheritDoc
     */
    public function __invoke(...$args)
    {
        $result = parent::__invoke($args);

        foreach ($this->queue as $callable) {
            $callable();
        }

        return $result;
    }

    /**
     * chain
     *
     * @param  callable  $callable
     *
     * @return  static
     */
    public function chain(callable $callable)
    {
        $this->queue[] = $callable;

        return $this;
    }

    /**
     * Method to get property Queue
     *
     * @return  array[callable]
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getQueue(): array
    {
        return $this->queue;
    }
}
