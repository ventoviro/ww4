<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Event;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Class EventInterface
 *
 * @since 2.0
 */
interface EventInterface extends StoppableEventInterface, \ArrayAccess
{
    /**
     * Get the event name.
     *
     * @return  string  The event name.
     *
     * @since   2.0
     */
    public function getName(): string;

    /**
     * Clone a new instance with new name. Use for pass Event to another new progress but keep arguments.
     *
     * ```php
     * $event = $dispatcher->emit(new Event('before.run'));
     *
     * // ...
     *
     * $event2 = $dispatcher->emit($event->mirror('after.run'));
     * ```
     *
     * @param  string  $name
     * @param  array   $args
     *
     * @return  static
     */
    public function mirror(string $name, array $args);

    /**
     * Stop the event propagation.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function stopPropagation(): void;

    /**
     * getArguments
     *
     * @return  array
     */
    public function &getArguments(): array;
}
