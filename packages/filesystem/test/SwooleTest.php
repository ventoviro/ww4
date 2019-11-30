<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Filesystem\Test;

use co;
use Co\Channel;
use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;
use Swoole\Coroutine\System;

/**
 * The SwooleTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class SwooleTest extends TestCase
{
    public function testReadFile()
    {
        go(function () {
            show(__DIR__ . '/../../../tmp/patch_notes_2_00.pdf');

            $f = Co::readFile(__DIR__ . '/../../../tmp/patch_notes_2_00.pdf');

            show('patch_notes_2_00.pdf', md5($f));
        });

        go(function () {
            show(__DIR__ . '/../../../tmp/201903.pdf');

            $f = Co::readFile(__DIR__ . '/../../../tmp/201903.pdf');

            show('201903.pdf', md5($f));
        });

        self::assertEquals('', '');
    }

    // public function testGo()
    // {
    //     $foo = '';
    //
    //     $chan = new Channel(1);
    //
    //     declare(ticks=1);
    //
    //     go(function () use ($chan) {
    //         while ($v = $chan->pop()) {
    //             echo $v;
    //         }
    //     });
    //
    //     go(function () {
    //         echo 'Flower';
    //     });
    //
    //     echo 'Sakura';
    //
    //     go(function () use ($chan) {
    //         $chan->push('Hello');
    //         $chan->push('asd');
    //         $chan->push('sdg');
    //     });
    //
    //     self::assertEquals('12', $foo);
    // }
}
