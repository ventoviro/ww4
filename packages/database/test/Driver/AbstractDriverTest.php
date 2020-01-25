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
use Windwalker\Database\Event\QueryEndEvent;
use Windwalker\Database\Exception\DatabaseQueryException;
use Windwalker\Database\Platform\AbstractPlatform;
use Windwalker\Database\Test\AbstractDatabaseDriverTestCase;
use Windwalker\Event\EventInterface;
use Windwalker\Utilities\TypeCast;

/**
 * The AbstractDriverTest class.
 */
abstract class AbstractDriverTest extends AbstractDatabaseDriverTestCase
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
            ->createQuery()
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

        $st = static::$driver->prepare('SELECT * FROM ww_flower WHERE id IN(?, ?, ?)')
            ->execute([1, 2, 3]);

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
                [
                    'id' => '3',
                    'title' => 'Anemone',
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
            $st->countAffected()
        );
    }

    public function testExecuteInsert(): void
    {
        $st = static::$driver->execute(
            'INSERT INTO ww_flower (title) VALUES (?)',
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
            $st->countAffected()
        );
    }

    public function testQueryFailed(): void
    {
        $sql = 'SELECT * FROM notexists WHERE foo = 123';

        $this->expectException(DatabaseQueryException::class);
        $this->expectExceptionMessageMatches(sprintf('/(%s)/', preg_quote($sql)));

        static::$driver->prepare($sql)->loadOne();
    }

    public function testEvents()
    {
        $stmt = static::$driver->prepare(
            $q = static::$driver->getDb()
                ->getQuery(true)
                ->select('*')
                ->from('ww_flower')
                ->where('id', 'in', [1, 2, 3])
                ->where('title', '!=', 'Hello')
        );

        $data = [];

        $stmt->on(
            QueryEndEvent::class,
            static function (EventInterface $event) use (&$data) {
                $data['end'] = $event['query']->render(true);
            }
        );

        $stmt->execute()->close();

        self::assertSqlEquals(
            $q->render(true),
            $data['end']
        );
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
            get_class(
                AbstractPlatform::create(
                    static::$driver->getPlatformName(),
                    static::$driver->getDb()
                )
            ),
            $platform
        );
    }

    /**
     * @see  AbstractDriver::quote
     */
    public function testQuote(): void
    {
        self::assertEquals(
            "'foo''s #hello --options'",
            static::$driver->quote("foo's #hello --options")
        );
    }

    /**
     * @see  AbstractDriver::escape
     */
    public function testEscape(): void
    {
        self::assertEquals(
            "foo''s #hello --options",
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

        if (!static::$driver->isSupported()) {
            self::markTestSkipped('Driver: ' . static::$driverName . ' not available.');
        }

        static::$driver->setPlatformName(static::$platform);
    }

    protected static function createDriver(?array $params = null): AbstractDriver
    {
        $params           = $params ?? self::getTestParams();
        $params['driver'] = static::$driverName;

        return (new DatabaseAdapter($params))->getDriver();
    }

    protected static function setupDatabase(): void
    {
        self::importFromFile(__DIR__ . '/../stub/' . static::$platform . '.sql');
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        static::$driver->disconnect();
        static::$driver = null;
    }
}
