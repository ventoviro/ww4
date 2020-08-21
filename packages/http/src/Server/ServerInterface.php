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

/**
 * Interface ServerInterface
 */
interface ServerInterface extends EventListenableInterface
{
    public function listen(string $host = '0.0.0.0', int $port = 0, int $options = 0): void;

    public function stop(): void;
}