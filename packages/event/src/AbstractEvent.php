<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Event;

use Windwalker\Utilities\Assert\ArgumentsAssert;

/**
 * The AbstractEvent class.
 */
abstract class AbstractEvent implements EventInterface
{
    /**
     * The event name.
     *
     * @var    string
     *
     * @since  2.0
     */
    protected ?string $name = null;

    /**
     * A flag to see if the event propagation is stopped.
     *
     * @var    boolean
     *
     * @since  2.0
     */
    protected bool $stopped = false;

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
        ArgumentsAssert::assert(
            is_string($event) || $event instanceof EventInterface,
            '{caller} argument 1 should be string or EventInterface, %s given.',
            $event
        );

        if (!$event instanceof EventInterface) {
            $class = class_exists($event) ? $event : static::class;

            $event = new $class($event);
        }

        $event->merge($args);

        return $event;
    }

    /**
     * Constructor.
     *
     * @param  string|null  $name       The event name.
     * @param  array        $arguments  The event arguments.
     *
     * @since   2.0
     */
    public function __construct(?string $name = null, array $arguments = [])
    {
        $this->name = $name ?? static::class;

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
    public function mirror(string $name, array $args = [])
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
    public function &getArguments(): array
    {
        $data = get_object_vars($this);

        unset($data['name'], $data['stopped']);

        return $data;
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
            $this->$key = &$value;
        }

        return $this;
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
     * Clear all event arguments.
     *
     * @return  static  Return self to support chaining.
     *
     * @since   2.0
     */
    public function clear()
    {
        $props = (new \ReflectionClass($this))->getDefaultProperties();

        unset($props['name'], $props['stopped']);

        foreach ($props as $field => $value) {
            $this->$field = $value;
        }

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
}
