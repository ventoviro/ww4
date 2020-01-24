<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver\Pdo;

use Windwalker\Data\Collection;
use Windwalker\Data\Format\PhpFormat;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Driver\Pdo\PdoDriver;
use Windwalker\Database\Driver\Pdo\PdoStatement;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The PdoDriverTest class.
 */
class PdoDriverTest extends AbstractDatabaseTestCase
{
    protected static $platform = 'mysql';

    /**
     * @var PdoDriver
     */
    protected static $driver;

    /**
     * @see  PdoDriver::prepare
     */
    public function testPrepare(): void
    {
        $st = static::$driver->prepare('SELECT * FROM ww_flower WHERE id <= :id')
            ->bind('id', 2);

        self::assertEquals(
            [
                [
                    'id' => '1',
                    'title' => 'Alstroemeria'
                ],
                [
                    'id' => '2',
                    'title' => 'Amaryllis'
                ]
            ],
            $st->fetchAll()
                ->mapProxy()
                ->only(['id', 'title'])
                ->dump(true)
        );
    }

    public function testPrepareBounded(): void
    {
        // Bind param
        $id = 1;
        $st = static::$driver->prepare('SELECT * FROM ww_flower WHERE id = :id')
            ->bindParam(':id', $id);

        self::assertEquals(
            'Alstroemeria',
            $st->fetchOne()->title
        );
        $id++;
        self::assertEquals(
            'Amaryllis',
            $st->fetchOne()->title
        );
        $id++;
        self::assertEquals(
            'Anemone',
            $st->fetchOne()->title
        );
    }

    /**
     * @see  PdoDriver::execute
     */
    public function testExecute(): void
    {
        $st = static::$driver->prepare('SELECT * FROM ww_flower WHERE id <= ?')
            ->execute([2]);

        self::assertEquals(
            [
                [
                    'id' => '1',
                    'title' => 'Alstroemeria'
                ],
                [
                    'id' => '2',
                    'title' => 'Amaryllis'
                ]
            ],
            $st->fetchAll()
                ->mapProxy()
                ->only(['id', 'title'])
                ->dump(true)
        );
    }

    /**
     * @see  PdoDriver::getConnection
     */
    public function testGetConnection(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PdoDriver::setConnection
     */
    public function testSetConnection(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PdoDriver::createConnection
     */
    public function testCreateConnection(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PdoDriver::setPlatformName
     */
    public function testSetPlatformName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PdoDriver::getPlatformName
     */
    public function testGetPlatformName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PdoDriver::disconnect
     */
    public function testDisconnect(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PdoDriver::__construct
     */
    public function test__construct(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PdoDriver::getPlatform
     */
    public function testGetPlatform(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PdoDriver::quote
     */
    public function testQuote(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PdoDriver::connect
     */
    public function testConnect(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PdoDriver::escape
     */
    public function testEscape(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = null;
    }

    protected function tearDown(): void
    {
    }

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$driver = new PdoDriver(new DatabaseAdapter(self::getTestParams()));

        static::$driver->setPlatformName(static::$platform);
    }

    protected static function setupDatabase(): void
    {
        self::importFromFile(__DIR__ . '/../../stub/mysql.sql');
    }
}
