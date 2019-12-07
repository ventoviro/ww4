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
interface EventInterface extends StoppableEventInterface
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
     * withNewName
     *
     * @param  string  $name
     *
     * @return  static
     */
    public function cloneNew(string $name);
}
