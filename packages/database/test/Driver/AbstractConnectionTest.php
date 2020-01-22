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
use Windwalker\Database\Driver\Mysqli\MysqliConnection;
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
    protected static $className = AbstractConnection::class;

    /**
     * @var AbstractConnection
     */
    protected $instance;

    protected static function setupDatabase(): void
    {
    }

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        $className = static::$className;

        if (!$className::isSupported()) {
            self::markTestSkipped('Driver for: ' . $className . ' not available.');
        }

        parent::setUpBeforeClass();
    }

    protected function setUp(): void
    {
        $this->instance = static::createConnection();
    }

    protected static function createConnection(): AbstractConnection
    {
        $className = static::$className;

        return new $className(self::getTestParams());
    }

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

        $conn->disconnect();

        self::assertNull($conn->getConnection());
    }
}
