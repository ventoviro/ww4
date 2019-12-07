<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Event;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Interface DispatcherInterface
 */
interface DispatcherInterface extends EventEmitterInterface, EventDispatcherInterface
{
    /**
     * Add a listener to this dispatcher, only if not already registered to these events.
     * If no events are specified, it will be registered to all events matching it's methods name.
     * In the case of a closure, you must specify at least one event name.
     *
     * @param  object|\Closure  $subscriber  The listener
     * @param  integer          $priority    The listener priority.
     *
     * @return  static  This method is chainable.
     *
     * @throws  \InvalidArgumentException
     *
     * @since   2.0
     */
    public function subscribe(object $subscriber, ?int $priority = null);

    /**
     * Add single listener.
     *
     * @param  string    $event
     * @param  callable  $callable
     * @param  int       $priority
     *
     * @return  static
     *
     * @since   3.0
     */
    public function on(string $event, callable $callable, ?int $priority = null);
}
