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
use Windwalker\Event\Listener\ListenerPriority;
use Windwalker\Event\Listener\ListenersQueue;

/**
 * The SubscribableListenerProviderInterface class.
 */
interface SubscribableListenerProviderInterface extends ListenerProviderInterface
{
    /**
     * on
     *
     * @param  string    $event
     * @param  callable  $listener
     * @param  int       $priority
     *
     * @param  bool      $once
     *
     * @return  void
     */
    public function on(string $event, callable $listener, ?int $priority = ListenerPriority::NORMAL, bool $once = false): void;

    /**
     * subscribe
     *
     * @param  object    $subscriber
     * @param  int|null  $priority
     *
     * @return  void
     */
    public function subscribe(object $subscriber, ?int $priority = null): void;

    /**
     * Method to get property Listeners
     *
     * @return  ListenersQueue[]
     *
     * @since  __DEPLOY_VERSION__
     */
    public function &getListeners(): array;
}
