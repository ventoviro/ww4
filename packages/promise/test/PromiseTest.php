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
use React\EventLoop\StreamSelectLoop;
use Swoole\Event;
use Windwalker\Promise\Async\AsyncInterface;
use Windwalker\Promise\Async\AsyncRunner;
use Windwalker\Promise\Async\DeferredAsync;
use Windwalker\Promise\Async\NoAsync;
use Windwalker\Promise\Async\TaskQueue;
use Windwalker\Promise\Promise;
use Windwalker\Test\TestHelper;

/**
 * The PromiseTest class.
 */
class PromiseTest extends TestCase
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        TaskQueue::getInstance()->disableShutdownRunner();

        AsyncRunner::getInstance()->setHandlers([
            new NoAsync()
        ]);
    }

    protected function setUp(): void
    {
        $this->values = [];

        parent::setUp();
    }

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

    public function testConstructorCoroutine(): void
    {
        $p = new Promise(function ($resolve) use (&$generator) {
            $generator = (static function () use ($resolve) {
                $resolve(yield);
            })();
        });

        $p->then(function ($v) {
            $this->values['v1'] = $v;
        });

        $generator->send('Flower');

        self::assertEquals('Flower', $this->values['v1']);
    }

    public function testRejected(): void
    {
        self::markTestIncomplete();
    }

    public function testRejectedWithoutCatch(): void
    {
        self::markTestSkipped('Enable this after async promise prepared');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Hello');

        Promise::rejected('Hello');
    }

    public function testEventLoop(): void
    {
        self::useHandler(new DeferredAsync());

        $loop = new StreamSelectLoop();

        $p = new Promise(static function (callable $resolve) {
            $resolve('Hello');
        });
        $p->then(function ($v) use ($loop) {
            $this->values['v1'] = $v;

            $loop->stop();
        });

        $loop->addPeriodicTimer(0, [TaskQueue::getInstance(), 'run']);
        $loop->run();

        self::assertEquals('Hello', $this->values['v1']);
    }

    /**
     * useHandler
     *
     * @param  AsyncInterface  $handler
     *
     * @return  void
     */
    protected static function useHandler(AsyncInterface $handler): void
    {
        AsyncRunner::getInstance()->setHandlers([$handler]);
    }
}
