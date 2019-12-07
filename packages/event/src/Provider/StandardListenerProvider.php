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
use Windwalker\Event\EventInterface;
use Windwalker\Event\EventSubscriberInterface;
use Windwalker\Event\Listener\ListenerPriority;
use Windwalker\Event\Listener\ListenersQueue;
use Windwalker\Utilities\StrNormalise;

/**
 * The SimpleListenerProvider class.
 */
class StandardListenerProvider implements ListenerProviderInterface
{
    /**
     * @var ListenersQueue[]
     */
    protected $listeners = [];

    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        if ($event instanceof EventInterface) {
            $eventName = $event->getName();
        } else {
            $eventName = get_class($event);
        }

        $eventName = strtolower($eventName);

        return $this->listeners[$eventName] ?? new ListenersQueue();
    }

    /**
     * @inheritDoc
     */
    public function on(string $event, callable $listener, ?int $priority = null): void
    {
        $priority = $priority ?? ListenerPriority::NORMAL;
        $event = strtolower($event);

        if (!$this->listeners[$event]) {
            $this->listeners[$event] = new ListenersQueue();
        }

        $this->listeners[$event]->add($listener, $priority);
    }

    /**
     * @inheritDoc
     */
    public function subscribe(object $subscriber, ?int $priority = null): void
    {
        if ($subscriber instanceof EventSubscriberInterface) {
            $events = $subscriber->getSubscribedEvents();
        } else {
            $methods = get_class_methods($subscriber);
            $events = [];

            foreach ($methods as $method) {
                $events[$method] = [static::normalize($method), $priority];
            }
        }

        foreach ($events as $event => $method) {
            // Register: ['eventName' => 'methodName']
            if (is_string($method)) {
                $this->on($event, [$subscriber, $method], $priority);
            } elseif (is_array($method) && $method !== []) {
                if (is_string($method[0])) {
                    // Register: ['eventName' => ['methodName', $priority]]
                    $this->on($event, [$subscriber, $method[0]], $method[1] ?? $priority);
                } elseif (is_array($method[0])) {
                    // Register: ['eventName' => [['methodName1', $priority], ['methodName2']]]
                    foreach ($method as $method2) {
                        $this->on($event, [$subscriber, $method2[0]], $method2[1] ?? $priority);
                    }
                }
            }
        }
    }

    /**
     * normalize
     *
     * @param  string  $methodName
     *
     * @return  string
     */
    private static function normalize(string $methodName): string
    {
        return lcfirst(StrNormalise::toCamelCase($methodName));
    }

    /**
     * Method to get property Listeners
     *
     * @return  ListenersQueue[]
     *
     * @since  __DEPLOY_VERSION__
     */
    public function &getListeners(): array
    {
        return $this->listeners;
    }
}
