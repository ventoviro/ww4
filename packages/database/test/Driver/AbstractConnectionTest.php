<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver;

use Windwalker\Database\Driver\AbstractConnection;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The AbstractConnectionTest class.
 */
abstract class AbstractConnectionTest extends AbstractDatabaseTestCase
{
    protected static $platform = '';

    /**
     * @var AbstractConnection
     */
    protected $instance;

    protected static function setupDatabase(): void
    {
    }

    protected function setUp(): void
    {
        $this->instance = static::createConnection();
    }

    abstract protected static function createConnection(): AbstractConnection;

    /**
     * assertConnected
     *
     * @param  AbstractConnection  $conn
     *
     * @return  void
     */
    abstract public function assertConnected(AbstractConnection $conn): void;

    public function testConnect(): void
    {
        $conn = $this->instance;
        $conn->connect();

        $this->assertConnected($conn);
    }

    public function testConnectWrong()
    {
        $conn = $this->instance;
        $conn->setOption('password', 'This is wrong password');

        $this->expectException(\RuntimeException::class);
        $conn->connect();
    }

    public function testDisconnect(): void
    {
        $conn = $this->instance;
        $conn->connect();

        $ref = \WeakReference::create($conn->getConnection());

        $conn->disconnect();

        self::assertNull($conn->getConnection());

        if (PHP_VERSION_ID >= 70400) {
            self::assertNull($ref->get());
        }
    }
}
