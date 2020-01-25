<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Event;

use Windwalker\Event\Event;

/**
 * The QueryFailedEvent class.
 */
class QueryFailedEvent extends Event
{
    /**
     * @inheritDoc
     */
    public function __construct(array $arguments = [])
    {
        parent::__construct(null, $arguments);
    }

    public function __destruct()
    {
        show(__METHOD__);
    }
}
