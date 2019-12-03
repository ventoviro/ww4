<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Promise\Promise;
use Windwalker\Test\TestHelper;

/**
 * The PromiseTest class.
 */
class PromiseTest extends TestCase
{
    public function testConstructorAndRun(): void
    {
        $foo = null;

        $p = new Promise(
            function () use (&$foo) {
                $foo = 'Hello';
            }
        );

        self::assertEquals('Hello', $foo);
    }

    public function testConstructorResolve(): void
    {
        // Resolve with value
        $p = new Promise(
            function ($resolve) {
                $resolve('Flower');
            }
        );

        self::assertEquals(Promise::FULFILLED, TestHelper::getValue($p, 'state'));
        self::assertEquals('Flower', TestHelper::getValue($p, 'value'));

        // Resolve with promise
        $p = new Promise(
            function ($resolve) {
                $resolve(
                    new Promise(
                        function ($resolve) {
                            $resolve('Sakura');
                        }
                    )
                );
            }
        );

        self::assertEquals('Sakura', TestHelper::getValue($p, 'value'));
    }
}
