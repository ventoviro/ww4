<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Queue\Event;

use Windwalker\Event\AbstractEvent;

/**
 * The WorkerLoopStartEvent class.
 */
class LoopStartEvent extends AbstractEvent
{
    use QueueEventTrait;
}
