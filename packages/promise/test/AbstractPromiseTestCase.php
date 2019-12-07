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
use Windwalker\Promise\Async\AsyncInterface;
use Windwalker\Promise\Async\AsyncRunner;
use Windwalker\Promise\Async\NoAsync;
use Windwalker\Promise\Async\TaskQueue;

/**
 * The PromiseTestTrait class.
 */
abstract class AbstractPromiseTestCase extends TestCase
{
    /**
     * @var array
     */
    protected $values = '';

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        TaskQueue::getInstance()->disableShutdownRunner();

        self::useHandler(new NoAsync());
    }

    protected function setUp(): void
    {
        $this->values = [];
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
