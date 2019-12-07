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
use Swoole\Event;
use Windwalker\Promise\Async\AsyncRunner;
use Windwalker\Promise\Async\DeferredAsync;
use Windwalker\Promise\Async\SwooleAsync;
use Windwalker\Promise\Async\TaskQueue;
use Windwalker\Promise\Promise;
use Windwalker\Reactor\Test\Traits\SwooleTestTrait;

/**
 * The AsyncPromiseTest class.
 */
class AsyncPromiseTest extends AbstractPromiseTestCase
{
    use SwooleTestTrait;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::useHandler(new DeferredAsync());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Ensure async events ran
        $this->nextTick();
    }

    /**
     * @see  AsyncPromise
     */
    public function testConstructorAsync(): void
    {
        $promise = new Promise(function ($resolve) {
            $resolve('Hello');
        });

        $promise->then(function ($v) {
            $this->values['v1'] = $v;
        });

        self::assertArrayNotHasKey('v1', $this->values);

        TaskQueue::getInstance()->run();

        self::assertEquals('Hello', $this->values['v1']);
    }

    public function testConstructorReturnPromise(): void
    {
        $promise = new Promise(function ($resolve) {
            $resolve(new Promise(function ($re, $rj) {
                $re('Flower');
            }));
        });

        $promise->then(function ($v) {
            $this->values['v1'] = $v;
        });

        self::assertArrayNotHasKey('v1', $this->values);

        TaskQueue::getInstance()->run();

        self::assertEquals('Flower', $this->values['v1']);
    }

    public function testThenReturnPromise(): void
    {
        $promise = new Promise(function ($resolve) {
            $resolve(new Promise(function ($re, $rj) {
                $re('Flower');
            }));
        });

        $promise->then(function ($v) {
            return new Promise(function ($re) {
                $re('YOO');
            });
        })
            ->then(function ($v) {
                $this->values['v1'] = $v;
            });

        self::assertArrayNotHasKey('v1', $this->values);

        TaskQueue::getInstance()->run();

        self::assertEquals('YOO', $this->values['v1']);
    }

    public function testSwooleAsync()
    {
        if (!SwooleAsync::isSupported()) {
            static::markTestSkipped('Swoole has not installed');
        }

        AsyncRunner::getInstance()->setHandlers(
            [
                new SwooleAsync()
            ]
        );

        go(function () {
            $promise = new Promise(function ($resolve) {
                $resolve(new Promise(function ($re, $rj) {
                    $re('Flower');
                }));
            });

            $value = $promise->then(function ($v) {
                return new Promise(function ($re) {
                    $re('YOO');
                });
            })
                ->then(function ($v) {
                    $this->values['v1'] = $v;

                    return 'GOO';
                })
                ->wait();

            self::assertEquals('YOO', $this->values['v1']);
            self::assertEquals('GOO', $value);
        });

        self::assertArrayNotHasKey('v1', $this->values);
    }
}
