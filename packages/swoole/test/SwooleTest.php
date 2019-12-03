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
use Swoole\Event;
use Swoole\Timer;

/**
 * The SwooleTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class SwooleTest extends TestCase
{
    // public function testEventWait()
    // {
    //     Timer::tick(2000, function ($id) {
    //         var_dump($id);
    //     });
    //
    //     Event::cycle(function () {
    //         echo "hello [1]\n";
    //         Event::cycle(function () {
    //             echo "hello [2]\n";
    //             Event::cycle(null);
    //         });
    //     });
    //
    //     Event::cycle(function () {
    //         echo "hello [3]\n";
    //         Event::cycle(null);
    //     });
    // }
}
