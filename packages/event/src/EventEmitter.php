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
use Windwalker\Event\Listener\ListenersQueue;
use Windwalker\Event\Provider\DecorateListenerProvider;
use Windwalker\Event\Provider\SubscribableListenerProvider;
use Windwalker\Event\Provider\SubscribableListenerProviderInterface;

/**
 * The AttachableEventDispatcher class.
 */
class EventEmitter extends EventDispatcher implements
    EventEmitterInterface,
    EventRegisterInterface,
    EventOnceInterface
{
    /**
     * @var SubscribableListenerProviderInterface
     */
    protected $queueHolder;

    /**
     * EventEmitter constructor.
     *
     * @param  SubscribableListenerProviderInterface  $pool
     */
    public function __construct(SubscribableListenerProviderInterface $pool = null)
    {
        $this->queueHolder = $pool ?: new SubscribableListenerProvider();

        parent::__construct(new DecorateListenerProvider($this->queueHolder));
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
                $this->remove($listener->getCallable());
            }

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
        $event = Event::wrap($event, $args);

        $this->dispatch($event);

        return $event;
    }

    /**
     * @inheritDoc
     */
    public function subscribe(object $subscriber, ?int $priority = null)
    {
        $this->getQueueHolder()->subscribe($subscriber, $priority);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function on(string $event, callable $callable, ?int $priority = null, bool $once = false)
    {
        $this->getQueueHolder()->on($event, $callable, $priority, $once);

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
     * @param  mixed  $listener
     *
     * @return  static
     *
     * @throws \ReflectionException
     */
    public function remove($listener)
    {
        if ($this->isSubscriber($listener)) {
            foreach ($this->getQueues() as $queue) {
                $this->offSubscriber($queue, $listener);
            }
        } else {
            foreach ($this->getQueues() as $queue) {
                $queue->remove($listener);
            }
        }

        return $this;
    }

    /**
     * offEvent
     *
     * @param  string|EventInterface  $event
     * @param  callable|object        $listener
     *
     * @return  static
     * @throws \ReflectionException
     */
    public function off($event, $listener = null)
    {
        $event = Event::wrap($event);

        $listeners = &$this->getQueues();

        if (!isset($listeners[$event->getName()])) {
            return $this;
        }

        if ($listener === null) {
            unset($listeners[$event->getName()]);
        } else {
            $queue = $listeners[$event->getName()];

            if ($this->isSubscriber($listener)) {
                $this->offSubscriber($queue, $listener);
            } else {
                $queue->remove($listener);
            }
        }

        return $this;
    }

    /**
     * offSubscriber
     *
     * @param  ListenersQueue  $queue
     * @param  object          $subscriber
     *
     * @return  void
     *
     * @throws \ReflectionException
     */
    private function offSubscriber(ListenersQueue $queue, object $subscriber): void
    {
        /** @var ListenerItem $listener */
        foreach ($queue as $listener) {
            $callable = $listener->getCallable();

            if ($callable instanceof \Closure) {
                $ref  = new \ReflectionFunction($callable);
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

    /**
     * isSubscriber
     *
     * @param mixed $listener
     *
     * @return  bool
     */
    private function isSubscriber($listener): bool
    {
        return !is_array($listener)
            && !is_string($listener)
            && !$listener instanceof \Closure;
    }

    /**
     * getListeners
     *
     * @param  string|EventInterface  $event
     *
     * @return  callable[]
     */
    public function getListeners($event): iterable
    {
        return $this->getQueueHolder()->getListenersForEvent(Event::wrap($event));
    }

    /**
     * getQueues
     *
     * @return  ListenersQueue[]
     */
    protected function &getQueues(): array
    {
        return $this->getQueueHolder()->getQueues();
    }

    /**
     * Method to get property Queues
     *
     * @return  SubscribableListenerProviderInterface
     */
    public function getQueueHolder(): SubscribableListenerProviderInterface
    {
        return $this->queueHolder;
    }

    /**
     * Method to set property queues
     *
     * @param  SubscribableListenerProviderInterface  $queueHolder
     *
     * @return  static  Return self to support chaining.
     */
    public function setQueueHolder(SubscribableListenerProviderInterface $queueHolder)
    {
        $this->queueHolder = $queueHolder;

        $this->listenerProvider = new DecorateListenerProvider($queueHolder);

        return $this;
    }
}
