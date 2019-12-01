<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise\Test;

use GuzzleHttp\Promise\Promise;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Promise\promise_for;

/**
 * The SwoolePromiseTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class PromiseTest extends TestCase
{
    public function testPromise()
    {
        $p = new \Windwalker\Promise\Promise(function ($ro, $rj) {
           show($ro, $rj);
        });

        $p->then(function ($v) {
            show($v);
        });

        $p->then(function ($v) {
            show($v);
        });

        show($p);
    }
}
