<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Queue\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Database\Test\AbstractDatabaseTestCase;
use Windwalker\Queue\Driver\DatabaseQueueDriver;
use Windwalker\Queue\Event\LoopEndEvent;
use Windwalker\Queue\QueueAdapter;
use Windwalker\Queue\Worker;

use function Windwalker\closure;

/**
 * The WorkerTest class.
 */
class WorkerTest extends AbstractDatabaseTestCase
{
    protected ?Worker $instance = null;

    /**
     * @see  Worker::loop
     */
    public function testLoop(): void
    {
        $file = __DIR__ . '/../tmp/worker.tmp';

        if (is_file($file)) {
            unlink($file);
        }

        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }

        $this->instance->getAdapter()->push(
            closure(
                function () use ($file) {
                    file_put_contents($file, 'Job executed.');
                }
            ),
            0,
            'hello'
        );

        $this->instance->on(LoopEndEvent::class, fn () => $this->instance->stop());

        $this->instance->loop(['default', 'hello'], ['sleep' => 0.1]);

        self::assertEquals(
            'Job executed.',
            file_get_contents($file)
        );
    }

    /**
     * @see  Worker::stopIfNecessary
     */
    public function testStopIfNecessary(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Worker::getState
     */
    public function testGetState(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Worker::stop
     */
    public function testStop(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Worker::runNextJob
     */
    public function testRunNextJob(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Worker::setState
     */
    public function testSetState(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Worker::process
     */
    public function testProcess(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Worker::__construct
     */
    public function test__construct(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Worker::shutdown
     */
    public function testShutdown(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Worker::getAdapter
     */
    public function testGetAdapter(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = new Worker(
            new QueueAdapter(
                new DatabaseQueueDriver(self::$db)
            )
        );
    }

    protected function tearDown(): void
    {
    }

    /**
     * setupDatabase
     *
     * @return  void
     */
    protected static function setupDatabase(): void
    {
        self::importFromFile(__DIR__ . '/../resources/sql/queue_jobs.sql');
    }
}
