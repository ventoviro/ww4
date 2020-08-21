<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Http\Server;

use Windwalker\Event\EventListenableInterface;
use Windwalker\Event\EventListenableTrait;

/**
 * The Server class.
 */
abstract class AbstractServer implements ServerInterface, EventListenableInterface
{
    use EventListenableTrait;
}
