<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Http\Event;

use Windwalker\Event\AbstractEvent;
use Windwalker\Event\Events\ErrorEventTrait;

/**
 * The ErrorEvent class.
 */
class ErrorEvent extends AbstractEvent
{
    use ErrorEventTrait;
}
