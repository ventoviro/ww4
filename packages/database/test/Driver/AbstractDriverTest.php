<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Driver\AbstractDriver;
use Windwalker\Database\Driver\DriverFactory;
use Windwalker\Database\Driver\Pdo\PdoSqlsrvConnection;
use Windwalker\Database\Platform\MysqlPlatform;
use Windwalker\Database\Test\AbstractDatabaseTestCase;
use Windwalker\Utilities\TypeCast;

/**
 * The AbstractDriverTest class.
 */
abstract class AbstractDriverTest extends AbstractDatabaseTestCase
{
    protected static $platform = '';

    protected static $driverName = '';

    /**
     * @var AbstractDriver
     */
    protected static $driver;

    /**
     * @see  AbstractDriver::prepare
     */
    public function testPrepare(): void
    {
        $st = static::$driver->prepare('SELECT * FROM ww_flower WHERE id <= :id')
            ->bind('id', 2);

        self::assertEquals(
            [
                [
                    'id' => '1',
                    'title' => 'Alstroemeria',
                ],
                [
                    'id' => '2',
                    'title' => 'Amaryllis',
                ],
            ],
            $st->loadAll()
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
            $st->loadOne()->title
        );
        $id++;
        self::assertEquals(
            'Amaryllis',
            $st->loadOne()->title
        );
        $id++;
        self::assertEquals(
            'Anemone',
            $st->loadOne()->title
        );
    }

    public function testPrepareWithQuery(): void
    {
        $id    = 1;
        $query = static::$driver->getPlatform()
            ->getQuery()
            ->select('*')
            ->from('ww_flower')
            ->whereRaw('id = :id')
            ->bindParam(':id', $id);

        // Bind param
        $st = static::$driver->prepare($query);

        self::assertEquals(
            'Alstroemeria',
            $st->loadOne()->title
        );
        $id++;
        self::assertEquals(
            'Amaryllis',
            $st->loadOne()->title
        );
        $id++;
        self::assertEquals(
            'Anemone',
            $st->loadOne()->title
        );
    }

    /**
     * @see  AbstractDriver::execute
     */
    public function testPrepareAndExecute(): void
    {
        $st = static::$driver->prepare('SELECT * FROM ww_flower WHERE id <= ?')
            ->execute([2]);

        self::assertEquals(
            [
                [
                    'id' => '1',
                    'title' => 'Alstroemeria',
                ],
                [
                    'id' => '2',
                    'title' => 'Amaryllis',
                ],
            ],
            $st->loadAll()
                ->mapProxy()
                ->only(['id', 'title'])
                ->dump(true)
        );
    }

    /**
     * @see  AbstractDriver::execute
     */
    public function testExecute(): void
    {
        $st = static::$driver->execute(
            'UPDATE ww_flower SET params = ? WHERE id <= ?',
            [
                'hello',
                3,
            ]
        );

        self::assertEquals(
            'hello',
            static::$driver->prepare(
                'SELECT params FROM ww_flower WHERE id = 1'
            )
                ->loadResult()
        );

        self::assertEquals(
            3,
            $st->count()
        );
    }

    public function testExecuteInsert(): void
    {
        $st = static::$driver->execute(
            'INSERT INTO ww_flower SET title = ?',
            [
                'Test',
            ]
        );

        self::assertEquals(
            86,
            static::$driver->lastInsertId()
        );

        self::assertEquals(
            1,
            $st->count()
        );
    }

    public function testCountResult(): void
    {
        $st = static::$driver->prepare('SELECT * FROM ww_flower WHERE id <= ?')
            ->execute([5]);

        self::assertEquals(5, $st->count());
    }

    public function testIterator(): void
    {
        $st = static::$driver->prepare('SELECT id, title FROM ww_flower WHERE id <= ?')
            ->execute([2]);

        $it = $st->getIterator();

        self::assertEquals(
            [
                [
                    'id' => '1',
                    'title' => 'Alstroemeria',
                ],
                [
                    'id' => '2',
                    'title' => 'Amaryllis',
                ],
            ],
            TypeCast::toArray($it, true)
        );
    }

    /**
     * @see  AbstractDriver::setPlatformName
     */
    public function testSetPlatformName(): void
    {
        $driver = self::createDriver([]);
        $driver->setPlatformName('sqlsrv');
        $conn = $driver->createConnection();

        self::assertInstanceOf(
            PdoSqlsrvConnection::class,
            $conn
        );
    }

    /**
     * @see  AbstractDriver::getPlatformName
     */
    public function testGetPlatformName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  AbstractDriver::disconnect
     */
    public function testDisconnect(): void
    {
        static::$driver->disconnect();

        self::assertFalse(static::$driver->getConnection()->isConnected());

        self::assertNull(static::$driver->getConnection()->get());
    }

    /**
     * @see  AbstractDriver::getPlatform
     */
    public function testGetPlatform(): void
    {
        $platform = static::$driver->getPlatform();

        self::assertInstanceOf(
            MysqlPlatform::class,
            $platform
        );
    }

    /**
     * @see  AbstractDriver::quote
     */
    public function testQuote(): void
    {
        self::assertEquals(
            "'foo\'s #hello --options'",
            static::$driver->quote("foo's #hello --options")
        );
    }

    /**
     * @see  AbstractDriver::escape
     */
    public function testEscape(): void
    {
        self::assertEquals(
            "foo\'s #hello --options",
            static::$driver->escape("foo's #hello --options")
        );
    }

    protected function setUp(): void
    {
        //
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

        static::$driver = self::createDriver();

        static::$driver->setPlatformName(static::$platform);
    }

    protected static function createDriver(?array $params = null): AbstractDriver
    {
        return DriverFactory::create(static::$driverName, new DatabaseAdapter($params ?? self::getTestParams()));
    }

    protected static function setupDatabase(): void
    {
        self::importFromFile(__DIR__ . '/../stub/' . static::$platform . '.sql');
    }
}
