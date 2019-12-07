<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise\Async;

/**
 * The AsyncHandler class.
 */
class AsyncRunner
{
    /**
     * @var AsyncInterface[]
     */
    protected $handlers = [];

    /**
     * @var static
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @param  static|null  $instance
     *
     * @return  static
     */
    public static function getInstance(?self $instance = null): self
    {
        if ($instance) {
            static::$instance = $instance;
        }

        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * AsyncRunner constructor.
     *
     * @param  AsyncInterface[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * run
     *
     * @param  callable  $callback
     *
     * @return  AsyncCursor
     */
    public function run(callable $callback): AsyncCursor
    {
        return $this->getAvailableHandler()->runAsync($callback);
    }

    /**
     * done
     *
     * @param  AsyncCursor|null  $cursor
     *
     * @return  void
     */
    public function done(?AsyncCursor $cursor): void
    {
        $this->getAvailableHandler()->done($cursor);
    }

    /**
     * wait
     *
     * @param  AsyncCursor  $cursor
     *
     * @return  void
     */
    public function wait(AsyncCursor $cursor): void
    {
        $this->getAvailableHandler()->wait($cursor);
    }

    /**
     * getAvailableHandler
     *
     * @return  AsyncInterface
     */
    public function getAvailableHandler(): AsyncInterface
    {
        foreach ($this->getHandlers() as $handler) {
            if ($handler::isSupported()) {
                return $handler;
            }
        }

        throw new \DomainException('No available async handlers');
    }

    /**
     * getHandlers
     *
     * @return  AsyncInterface[]
     */
    public function getHandlers(): array
    {
        if ($this->handlers === []) {
            $this->handlers = [
                new SwooleAsync(),
                new DeferredAsync()
            ];
        }

        return $this->handlers;
    }

    /**
     * Method to set property handlers
     *
     * @param  AsyncInterface[]  $handlers
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setHandlers(array $handlers)
    {
        $this->handlers = $handlers;

        return $this;
    }
}
