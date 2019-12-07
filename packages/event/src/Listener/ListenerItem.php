<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Event\Listener;

/**
 * The ListenerItem class.
 */
class ListenerItem
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var bool
     */
    protected $once;

    /**
     * ListenerItem constructor.
     *
     * @param  callable  $callable
     * @param  int       $priority
     * @param  bool      $once
     */
    public function __construct(callable $callable, ?int $priority, bool $once)
    {
        $this->callable = $callable;
        $this->priority = $priority ?? ListenerPriority::NORMAL;
        $this->once     = $once;
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
        $callable = $this->callable;

        return $callable(...$args);
    }

    /**
     * is
     *
     * @param  callable  $callable
     *
     * @return  bool
     */
    public function is(callable $callable): bool
    {
        if ($callable instanceof static) {
            $callable = $callable->getCallable();
        }

        return $callable === $this->callable;
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

    /**
     * Method to get property Priority
     *
     * @return  int
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Method to get property Once
     *
     * @return  bool
     *
     * @since  __DEPLOY_VERSION__
     */
    public function isOnce(): bool
    {
        return $this->once;
    }
}
