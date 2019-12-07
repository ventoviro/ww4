<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Event;

use Windwalker\Utilities\AccessibleTrait;
use Windwalker\Utilities\Contract\AccessibleInterface;

/**
 * Class Event
 *
 * @since 2.0
 */
class Event implements EventInterface, AccessibleInterface, \Serializable
{
    use AccessibleTrait;

    /**
     * The event name.
     *
     * @var    string
     *
     * @since  2.0
     */
    protected $name;

    /**
     * A flag to see if the event propagation is stopped.
     *
     * @var    boolean
     *
     * @since  2.0
     */
    protected $stopped = false;

    /**
     * @var bool
     */
    protected $once = false;

    /**
     * wrap
     *
     * @param  string|EventInterface  $event
     * @param  array                  $args
     *
     * @return  EventInterface
     */
    public static function wrap($event, array $args = []): EventInterface
    {
        if (!$event instanceof EventInterface) {
            $event = new static($event);
        }

        $event->merge($args);

        return $event;
    }

    /**
     * Constructor.
     *
     * @param  string  $name       The event name.
     * @param  array   $arguments  The event arguments.
     *
     * @since   2.0
     */
    public function __construct($name, array $arguments = [])
    {
        $this->name = $name;

        $this->merge($arguments);
    }

    /**
     * Get the event name.
     *
     * @return  string  The event name.
     *
     * @since   2.0
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function cloneNew(string $name, array $args = [])
    {
        $new = clone $this;

        $new->name    = $name;
        $new->stopped = false;
        $new->merge($args);

        return $new;
    }

    /**
     * Method to set property name
     *
     * @param  string  $name
     *
     * @return  static  Return self to support chaining.
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get all event arguments.
     *
     * @return  array  An associative array of argument names as keys
     *                 and their values as values.
     *
     * @since   2.0
     */
    public function getArguments(): array
    {
        return $this->storage;
    }

    /**
     * Method to set property arguments
     *
     * @param  array  $arguments   An associative array of argument names as keys
     *                             and their values as values.
     *
     * @return  static  Return self to support chaining.
     */
    public function setArguments(array $arguments)
    {
        $this->clear();

        $this->merge($arguments);

        return $this;
    }

    /**
     * mergeArguments
     *
     * @param  array  $arguments
     *
     * @return  static
     */
    public function merge(array $arguments)
    {
        foreach ($arguments as $key => &$value) {
            $this->storage[$key] = &$value;
        }

        return $this;
    }

    /**
     * Clear all event arguments.
     *
     * @return  static  Return self to support chaining.
     *
     * @since   2.0
     */
    public function clear()
    {
        // Break the reference
        unset($this->storage);

        $this->storage = [];

        return $this;
    }

    /**
     * Stop the event propagation.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function stopPropagation(): void
    {
        $this->stopped = true;
    }

    /**
     * Tell if the event propagation is stopped.
     *
     * @return  boolean  True if stopped, false otherwise.
     *
     * @since   2.0
     */
    public function isPropagationStopped(): bool
    {
        return true === $this->stopped;
    }

    /**
     * Serialize the event.
     *
     * @return  string  The serialized event.
     *
     * @since   2.0
     */
    public function serialize()
    {
        return serialize([$this->name, $this->storage, $this->stopped]);
    }

    /**
     * Unserialize the event.
     *
     * @param  string  $serialized  The serialized event.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function unserialize($serialized)
    {
        [$this->name, $this->storage, $this->stopped] = unserialize($serialized);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'stopped' => $this->stopped,
            'once' => $this->once,
            'arguments' => $this->storage,
        ];
    }
}
