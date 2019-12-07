<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Windwalker\Event\Provider\SubscribableListenerProvider;

/**
 * The EventDispatcher class.
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var ListenerProviderInterface
     */
    protected $listenerProvider;

    /**
     * EventDispatcher constructor.
     *
     * @param  ListenerProviderInterface  $listenerProvider
     */
    public function __construct(ListenerProviderInterface $listenerProvider = null)
    {
        $this->listenerProvider = $listenerProvider ?? new SubscribableListenerProvider();
    }

    /**
     * @inheritDoc
     */
    public function dispatch(object $event)
    {
        $stoppable = $event instanceof StoppableEventInterface;

        if ($stoppable && $event->isPropagationStopped()) {
            return $event;
        }

        foreach ($this->getListenerProvider()->getListenersForEvent($event) as $listener) {
            $listener($event);

            $stoppable = $event instanceof StoppableEventInterface;

            if ($stoppable && $event->isPropagationStopped()) {
                return $event;
            }
        }

        return $event;
    }

    /**
     * Method to get property ListenerProvider
     *
     * @return  ListenerProviderInterface
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getListenerProvider(): ListenerProviderInterface
    {
        return $this->listenerProvider;
    }

    /**
     * Method to set property listenerProvider
     *
     * @param  ListenerProviderInterface  $listenerProvider
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setListenerProvider(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;

        return $this;
    }
}
