<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Event\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;
use Windwalker\Utilities\Assert\ArgumentsAssert;

/**
 * The SimpleListenerProvider class.
 */
class SimpleListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * SimpleListenerProvider constructor.
     *
     * @param  array  $listeners
     */
    public function __construct(array $listeners)
    {
        $this->setListeners($listeners);
    }

    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->listeners as $eventType => $listeners) {
            if (!$event instanceof $eventType) {
                continue;
            }

            foreach ($listeners as $listener) {
                yield $listener;
            }
        }
    }

    /**
     * add
     *
     * @param  string    $event
     * @param  callable  $listener
     *
     * @return  static
     */
    public function add(string $event, callable $listener)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;

        return $this;
    }

    /**
     * Method to get property Listeners
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }

    /**
     * Method to set property listeners
     *
     * @param  array  $listeners
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setListeners(array $listeners)
    {
        foreach ($listeners as $event => $listenerQueue) {
            ArgumentsAssert::assert(
                is_array($listenerQueue) && !is_callable($listenerQueue),
                'Event ' . $event . ' listeners should be array, %2$s given',
                $listenerQueue
            );

            foreach ($listenerQueue as $i => $listener) {
                ArgumentsAssert::assert(
                    is_callable($listener),
                    'Listener ' . $event . ' => ' . $i . ' should be callable, %2$s given',
                    $listener
                );
            }
        }

        $this->listeners = $listeners;

        return $this;
    }
}
