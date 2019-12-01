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

use function Windwalker\Promise\nope;

/**
 * The SwoolePromiseTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class PromiseTest extends TestCase
{
    public function testConstructorAndRun(): void
    {
        $foo = null;

        $p = new Promise(function () use (&$foo) {
            $foo = 'Hello';
        });

        self::assertEquals('Hello', $foo);
    }

    public function testConstructorResolve(): void
    {
        // Resolve with value
        $p = new Promise(function ($resolve) {
            $resolve('Flower');
        });

        self::assertEquals(Promise::FULFILLED, TestHelper::getValue($p, 'state'));
        self::assertEquals('Flower', TestHelper::getValue($p, 'value'));

        // Resolve with promise
        $p = new Promise(function ($resolve) {
            $resolve(new Promise(function ($resolve) {
                $resolve('Sakura');
            }));
        });

        self::assertEquals('Sakura', TestHelper::getValue($p, 'value'));
    }

    /**
     * @throws \ReflectionException
     * @see  Promise::then
     */
    public function testThenPending(): void
    {
        $p = new Promise(nope());

        $p2 = $p->then($rsv1 = nope(), $rej1 = nope());
        $p->then($rsv2 = nope(), $rej2 = nope());

        $p3 = $p2->then($rsv3 = nope(), $rej3 = nope());

        self::assertNotSame($p2, $p);

        // Handlers
        $handlers = TestHelper::getValue($p, 'handlers');

        self::assertSame($handlers[0][0], $p2);
        self::assertSame($handlers[0][1], $rsv1);
        self::assertSame($handlers[0][2], $rej1);
        self::assertSame($handlers[1][1], $rsv2);

        $handlers = TestHelper::getValue($p2, 'handlers');

        self::assertSame($handlers[0][0], $p3);
        self::assertSame($handlers[0][1], $rsv3);
        self::assertSame($handlers[0][2], $rej3);
    }

    /**
     * @throws \ReflectionException
     * @see  Promise::then
     */
    public function testThenAlreadyFulfilled(): void
    {
        $p = new Promise(function ($resolve, $reject) {
            $resolve(1);
        });

        $state = TestHelper::getValue($p, 'state');

        self::assertEquals(Promise::FULFILLED, $state);

        $p2 = $p
            ->then(function ($v) {
                return ++$v;
            })
            ->then(function ($v) {
                return ++$v;
            });

        self::assertEquals(3, TestHelper::getValue($p2, 'value'));

        // Test return new Promise
        $p3 = $p->then(function ($v) {
            return new Promise(function ($resolve) {
                $resolve('Hello');
            });
        });

        $newValue = null;

        $p4 = $p3->then(function ($v2) use (&$newValue) {
            $newValue = $v2;
        });

        self::assertEquals('Hello', TestHelper::getValue($p3, 'value'));
        self::assertNull(TestHelper::getValue($p4, 'value'));
        self::assertEquals('Hello', $newValue);
    }

    /**
     * @throws \ReflectionException
     * @see  Promise::then
     */
    public function testThenAlreadyRejected(): void
    {
        $p = new Promise(function ($resolve, $reject) {
            $reject(new \Exception('Sakura'));
        });

        $state = TestHelper::getValue($p, 'state');

        self::assertEquals(Promise::REJECTED, $state);

        $p2 = $p
            ->then(nope(), function () {

            })
            ->then(nope(), function ($v) {
                return ++$v;
            });

        self::assertEquals(3, TestHelper::getValue($p2, 'value'));

        // Test return new Promise
        $p3 = $p->then(function ($v) {
            return new Promise(function ($resolve) {
                $resolve('Hello');
            });
        });

        $newValue = null;

        $p4 = $p3->then(function ($v2) use (&$newValue) {
            $newValue = $v2;
        });

        self::assertEquals('Hello', TestHelper::getValue($p3, 'value'));
        self::assertNull(TestHelper::getValue($p4, 'value'));
        self::assertEquals('Hello', $newValue);
    }
}
