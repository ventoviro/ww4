<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Reactor\Test\Traits;

use Swoole\Event;

/**
 * The SwooleTestTrait class.
 */
trait SwooleTestTrait
{
    public function nextTick(): void
    {
        if ($this->swooleEnabled()) {
            Event::wait();
        }
    }

    /**
     * swooleEnabled
     *
     * @return  bool
     */
    public function swooleEnabled(): bool
    {
        return extension_loaded('swoole');
    }
}
