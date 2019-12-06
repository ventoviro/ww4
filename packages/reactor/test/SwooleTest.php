<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Swoole\Test;

use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;
use Swoole\Event;
use Swoole\Timer;

/**
 * The SwooleTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class SwooleTest extends TestCase
{
    public function testEventWait()
    {
        go(function () {
            Coroutine::sleep(0.001);
            show('In');
        });

        show('Out');
    }
}
