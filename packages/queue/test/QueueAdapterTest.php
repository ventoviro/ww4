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
use Windwalker\Queue\Job\JobInterface;
use Windwalker\Queue\QueueAdapter;
use Windwalker\Queue\Test\Stub\TestJob;

/**
 * The QueueAdapterTest class.
 */
class QueueAdapterTest extends AbstractDatabaseTestCase
{
    protected ?QueueAdapter $instance;

    /**
     * @see  QueueAdapter::push
     */
    public function testPush(): void
    {
        $job = new TestJob(['Hello']);

        $result = $this->instance->push($job, 0);

        self::assertEquals(
            '1',
            $result
        );

        $job = self::$db->select('*')
            ->from('queue_jobs')
            ->where('id', 1)
            ->get();

        self::assertEquals(
            'default',
            $job->queue
        );

        $body = json_decode($job->body, true);

        self::assertEquals(
            ['Hello'],
            unserialize($body['job'])->logs
        );
    }

    /**
     * @see  QueueAdapter::pop
     */
    public function testPop(): void
    {
        $message = $this->instance->pop();

        self::assertEquals(
            1,
            $message->getAttempts()
        );

        /** @var TestJob $job */
        $job = unserialize($message->getSerializedJob());
        $job->execute();

        self::assertEquals(
            ['Hello'],
            $job->executed
        );
    }

    /**
     * @see  QueueAdapter::setDriver
     */
    public function testSetDriver(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  QueueAdapter::release
     */
    public function testRelease(): void
    {
        $job = new TestJob(['Welcome']);

        $result = $this->instance->push($job, 0);

        $message = $this->instance->pop();

        $r = self::$db->select('*')
            ->from('queue_jobs')
            ->where('id', 2)
            ->get();

        self::assertNotNull($r);

        $this->instance->release($message);

        $item = self::$db->select('*')
            ->from('queue_jobs')
            ->where('id', 2)
            ->get();

        self::assertNull($item->reserved);
    }

    /**
     * @see  QueueAdapter::delete
     */
    public function testDelete(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  QueueAdapter::getDriver
     */
    public function testGetDriver(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  QueueAdapter::__construct
     */
    public function test__construct(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  QueueAdapter::getMessageByJob
     */
    public function testGetMessageByJob(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  QueueAdapter::pushRaw
     */
    public function testPushRaw(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = new QueueAdapter(
            new DatabaseQueueDriver(static::$db)
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
