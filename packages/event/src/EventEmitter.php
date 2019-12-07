<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Windwalker\Event\Listener\ListenerItem;
use Windwalker\Event\Provider\DecorateListenerProvider;
use Windwalker\Event\Provider\SubscribableListenerProviderInterface;
use Windwalker\Utilities\Assert\ArgumentsAssert;

/**
 * The AttachableEventDispatcher class.
 */
class EventEmitter extends EventDispatcher implements EventEmitterInterface, EventSubscribableInterface
{
    /**
     * @var SubscribableListenerProviderInterface
     */
    protected $queues;

    /**
     * EventEmitter constructor.
     *
     * @param  SubscribableListenerProviderInterface  $queues
     */
    public function __construct(SubscribableListenerProviderInterface $queues)
    {
        $this->queues = $queues;

        parent::__construct(new DecorateListenerProvider($queues));
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

        /** @var ListenerItem $listener */
        foreach ($this->getListenerProvider()->getListenersForEvent($event) as $listener) {
            $listener->getCallable()($event);

            if ($listener->isOnce()) {
                $this->off($listener->getCallable());
            }

            $stoppable = $event instanceof StoppableEventInterface;

            if ($stoppable && $event->isPropagationStopped()) {
                return $event;
            }
        }

        return $event;
    }

    /**
     * @inheritDoc
     */
    public function emit($event, $args = []): EventInterface
    {
        ArgumentsAssert::assert(
            is_string($event) || $event instanceof EventInterface,
            '%s argument 1 should be string or EventInterface, %s given.',
            $event
        );

        if (!$event instanceof EventInterface) {
            $event = new Event($event);
        }

        $event->merge($args);

        $this->dispatch($event);

        return $event;
    }

    /**
     * @inheritDoc
     */
    public function subscribe(object $subscriber, ?int $priority = null)
    {
        $this->getQueues()->subscribe($subscriber, $priority);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function on(string $event, callable $callable, ?int $priority = null, bool $once = false)
    {
        $this->getQueues()->on($event, $callable, $priority, $once);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function once(string $event, callable $callable, ?int $priority = null)
    {
        return $this->on($event, $callable, $priority, true);
    }

    /**
     * off
     *
     * @param mixed  $listenerOrSubscriber
     *
     * @return  static
     *
     * @throws \ReflectionException
     */
    public function off($listenerOrSubscriber)
    {
        if (
            is_array($listenerOrSubscriber)
            || is_string($listenerOrSubscriber)
            || $listenerOrSubscriber instanceof \Closure
        ) {
            $this->offFunction($listenerOrSubscriber);
        } else {
            $this->offSubscriber($listenerOrSubscriber);
        }

        return $this;
    }

    /**
     * offEvent
     *
     * @param string|EventInterface $event
     *
     * @return  static
     */
    public function offEvent($event)
    {
        $event = Event::wrap($event);

        $listener = $this->getQueues()->getListeners();

        if (isset($listener[$event->getName()])) {
            unset($listener[$event->getName()]);
        }

        return $this;
    }

    /**
     * offFunction
     *
     * @param  callable  $callable
     *
     * @return  void
     */
    private function offFunction(callable $callable): void
    {
        foreach ($this->getQueues()->getListeners() as $queue) {
            $queue->remove($callable);
        }
    }

    /**
     * offSubscriber
     *
     * @param  object  $subscriber
     *
     * @return  void
     *
     * @throws \ReflectionException
     */
    private function offSubscriber(object $subscriber): void
    {
        foreach ($this->getQueues()->getListeners() as $event => $queue) {
            /** @var ListenerItem $listener */
            foreach ($queue as $listener) {
                $callable = $listener->getCallable();

                if ($callable instanceof \Closure) {
                    $ref = new \ReflectionFunction($listener);
                    $that = $ref->getClosureThis();
                } elseif (is_array($callable)) {
                    $that = $callable[0];
                } else {
                    continue;
                }

                if ($that === $subscriber) {
                    $queue->remove($listener);
                }
            }
        }
    }

    /**
     * getListeners
     *
     * @param string|EventInterface $event
     *
     * @return  callable[]
     */
    public function getListeners($event): iterable
    {
        return $this->getQueues()->getListenersForEvent(Event::wrap($event));
    }

    /**
     * Method to get property Queues
     *
     * @return  SubscribableListenerProviderInterface
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getQueues(): SubscribableListenerProviderInterface
    {
        return $this->queues;
    }

    /**
     * Method to set property queues
     *
     * @param  SubscribableListenerProviderInterface  $queues
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setQueues(SubscribableListenerProviderInterface $queues)
    {
        $this->queues = $queues;

        $this->listenerProvider = new DecorateListenerProvider($queues);

        return $this;
    }
}
