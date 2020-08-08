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
use Windwalker\Event\Events\ErrorEventTrait;
use Windwalker\Event\Events\MessageEventTrait;

/**
 * The JobFailure class.
 */
class JobFailureEvent extends AbstractEvent
{
    use JobEventTrait;
    use ErrorEventTrait;
}
