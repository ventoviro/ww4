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
use Rx\Observable;
use Rx\ObserverInterface;
use Windwalker\Event\Provider\CompositeListenerProvider;
use Windwalker\Utilities\Proxy\ChainableCallable;
use Windwalker\Utilities\Proxy\DisposableCallable;
use Windwalker\Utilities\Proxy\TimesLimitedCallable;
use function Windwalker\disposable;
use function Windwalker\tap;

/**
 * The AttachableEventDispatcher class.
 */
class EventEmitter extends EventDispatcher implements
    EventEmitterInterface,
    EventAttachableInterface,
    EventDisposableInterface
{
    /**
     * @var CompositeListenerProvider
     */
    protected $provider;

    /**
     * @var EventDispatcherInterface[]
     */
    protected $dealers = [];

    /**
     * EventEmitter constructor.
     *
     * @param  ListenerProviderInterface  $provider
     */
    public function __construct(ListenerProviderInterface $provider = null)
    {
        parent::__construct(CompositeListenerProvider::create($provider));
    }

    /**
     * @inheritDoc
     */
    public function dispatch(object $event)
    {
        return tap(parent::dispatch($event), function () use ($event) {
            foreach ($this->dealers as $dealer) {
                $dealer->dispatch($event);
            }
        });
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
        $this->provider->subscribe($subscriber, $priority);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function on(string $event, callable $callable, ?int $priority = null)
    {
        $this->provider->on($event, $callable, $priority);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function once(string $event, callable $callable, ?int $priority = null)
    {
        $this->on($event, disposable($callable), $priority);

        return $this;
    }

    /**
     * observe
     *
     * @param  string    $event
     * @param  int|null  $priority
     *
     * @return  Observable
     */
    public function observe(string $event, ?int $priority = null): Observable
    {
        if (!class_exists(Observable::class)) {
            throw new \DomainException('Please install reactivex/rxphp to support Observable.');
        }

        return Observable::create(function (ObserverInterface $subscriber) use ($event, $priority) {
            $this->on(
                $event,
                static function (EventInterface $event) use ($subscriber) {
                    $subscriber->onNext($event);
                },
                $priority
            );
        });
    }

    /**
     * off
     *
     * @param  callable|object  $listenerOrSubscriber
     *
     * @return  static
     *
     * @throws \ReflectionException
     */
    public function remove($listenerOrSubscriber)
    {
        $this->provider->remove($listenerOrSubscriber);

        return $this;
    }

    /**
     * offEvent
     *
     * @param  string|EventInterface  $event
     * @param  callable|object        $listenerOrSubscriber
     *
     * @return  static
     * @throws \ReflectionException
     */
    public function off($event, $listenerOrSubscriber = null)
    {
        $this->provider->off($event, $listenerOrSubscriber);

        return $this;
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
        return $this->provider->getListenersForEvent(Event::wrap($event));
    }

    /**
     * appendProvider
     *
     * @param  ListenerProviderInterface  $provider
     *
     * @return  static
     */
    public function appendProvider(ListenerProviderInterface $provider)
    {
        $this->provider->appendProvider($provider);

        return $this;
    }

    /**
     * registerDealer
     *
     * @param  EventDispatcherInterface  $dispatcher
     *
     * @return  static
     */
    public function registerDealer(EventDispatcherInterface $dispatcher)
    {
        $this->dealers[] = $dispatcher;

        return $this;
    }

    /**
     * resetDealers
     *
     * @return  static
     */
    public function resetDealers()
    {
        $this->dealers = [];

        return $this;
    }
}
