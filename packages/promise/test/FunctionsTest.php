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

use Windwalker\Promise\Async\AsyncRunner;
use Windwalker\Promise\Async\DeferredAsync;
use Windwalker\Promise\Async\SwooleAsync;
use Windwalker\Promise\Async\TaskQueue;

use Windwalker\Promise\ExtendedPromiseInterface;
use Windwalker\Promise\Promise;

use Windwalker\Reactor\Test\Traits\SwooleTestTrait;

use function Windwalker\Promise\async;
use function Windwalker\Promise\await;

/**
 * The FunctionsTest class.
 */
class FunctionsTest extends TestCase
{
    use SwooleTestTrait;

    protected $values = [];

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        TaskQueue::getInstance()->disableShutdownRunner();

        AsyncRunner::getInstance()->setHandlers(
            [
                new DeferredAsync()
            ]
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->values = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Ensure async events ran
        $this->nextTick();
    }

    public function testAsync()
    {
        AsyncRunner::getInstance()->setHandlers(
            [
                new SwooleAsync()
            ]
        );

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
