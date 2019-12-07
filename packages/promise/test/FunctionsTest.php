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
use Windwalker\Promise\Async\AsyncInterface;
use Windwalker\Promise\Async\AsyncRunner;
use Windwalker\Promise\Async\DeferredAsync;
use Windwalker\Promise\Async\NoAsync;
use Windwalker\Promise\Async\SwooleAsync;
use Windwalker\Promise\Async\TaskQueue;

use Windwalker\Promise\ExtendedPromiseInterface;
use Windwalker\Promise\Promise;

use Windwalker\Reactor\Test\Traits\SwooleTestTrait;

use function Windwalker\Promise\async;
use function Windwalker\Promise\await;
use function Windwalker\Promise\coroutine;
use function Windwalker\Promise\coroutineable;

/**
 * The FunctionsTest class.
 */
class FunctionsTest extends AbstractPromiseTestCase
{
    use SwooleTestTrait;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::useHandler(new DeferredAsync());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Ensure async events ran
        $this->nextTick();
    }

    public function testAsync()
    {
        static::useHandler(new SwooleAsync());

        go(function () {
            $p = async(function () {
                $this->values['v1'] = 'Flower';

                return 'Sakura';
            });

            self::assertArrayNotHasKey('v1', $this->values);

            self::assertEquals('Sakura', $p->wait());
        });
    }

    public function testAwait()
    {
        static::useHandler(new SwooleAsync());

        async(function () {
            $this->values['v1'] = await($this->runAsync('Sakura'));
            $this->values['v2'] = await($this->runAsync('Sunflower'));

            self::assertEquals('Sakura', $this->values['v1']);
            self::assertEquals('Sunflower', $this->values['v1']);

            return 'Lilium';
        })
            ->then(function ($v) {
                self::assertEquals('Lilium', $v);
            });
    }

    /**
     * testCoroutine
     *
     * @return  void
     *
     * @throws \Throwable
     */
    public function testCoroutine(): void
    {
        static::useHandler(new NoAsync());

        $v = coroutine(function () {
            $v1 = yield $this->runAsync('Sakura');
            $v2 = yield $this->runAsync('Rose');

            return $v1 . ' ' . $v2;
        })->wait();

        self::assertEquals('Sakura Rose', $v);

        static::useHandler(new SwooleAsync());

        go(function () {
            $v = coroutine(function () {
                $v1 = yield $this->runAsync('Sakura');
                $v2 = yield $this->runAsync('Rose');

                return $v1 . ' ' . $v2;
            })->wait();

            self::assertEquals('Sakura Rose', $v);
        });
    }

    /**
     * testCoroutineable
     *
     * @return  void
     *
     * @throws \Throwable
     */
    public function testCoroutineable(): void
    {
        static::useHandler(new NoAsync());

        $c = coroutineable(function ($arg) {
            $v1 = yield $this->runAsync($arg);
            $v2 = yield $this->runAsync('Rose');

            return $v1 . ' ' . $v2;
        });

        self::assertEquals('Sakura Rose', $c('Sakura')->wait());
    }



    /**
     * runAsync
     *
     * @param mixed $value
     *
     * @return  ExtendedPromiseInterface
     */
    protected function runAsync($value): ExtendedPromiseInterface
    {
        return async(static function () use ($value) {
            return $value;
        });
    }
}
