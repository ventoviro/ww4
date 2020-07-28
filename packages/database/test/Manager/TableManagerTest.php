<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Manager;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Manager\TableManager;
use PHPUnit\Framework\TestCase;
use Windwalker\Database\Schema\Schema;
use Windwalker\Database\Test\AbstractDatabaseTestCase;
use Windwalker\Test\TestHelper;

class TableManagerTest extends AbstractDatabaseTestCase
{
    protected ?TableManager $instance;

    /**
     * @see  TableManager::save
     */
    public function testSave(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getColumns
     */
    public function testGetColumns(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getColumnNames
     */
    public function testGetColumnNames(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::dropColumn
     */
    public function testDropColumn(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::dropIndex
     */
    public function testDropIndex(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::setName
     */
    public function testSetName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::addIndex
     */
    public function testAddIndex(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getDetail
     */
    public function testGetDetail(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::update
     */
    public function testUpdate(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::modifyColumn
     */
    public function testModifyColumn(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getSchemaObject
     */
    public function testGetSchemaObject(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::create
     */
    public function testCreate(): void
    {
        $table = self::$db->getTable('hello');

        $table->create(
            static function (Schema $schema) {
                $schema->primary('id');
                $schema->char('type')->length(25);
                $schema->integer('catid')->nullable(true);
                $schema->varchar('alias');
                $schema->varchar('title')->defaultValue('H');
                $schema->decimal('price')->length('20,6');
                $schema->text('intro');

                $schema->addIndex(['catid', 'type']);
                $schema->addIndex('title(150)');
                $schema->addUniqueKey('alias');
            }
        );

        self::assertSqlFormatEquals(
            <<<SQL
            CREATE TABLE IF NOT EXISTS `hello` (
            `id` int(11) NOT NULL,
            `type` char(25) NOT NULL DEFAULT '',
            `catid` int(11) DEFAULT NULL,
            `alias` varchar(255) NOT NULL DEFAULT '',
            `title` varchar(255) NOT NULL DEFAULT 'H',
            `price` decimal(20,6) NOT NULL DEFAULT 0,
            `intro` text NOT NULL DEFAULT ''
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ALTER TABLE `hello`
            ADD CONSTRAINT PRIMARY KEY (`id`),
            MODIFY COLUMN `id` int(11) NOT NULL AUTO_INCREMENT,
            ADD INDEX `idx_hello_catid_type` (`catid`,`type`),
            ADD INDEX `idx_hello_title` (`title`(150)),
            ADD CONSTRAINT `idx_hello_alias` UNIQUE (`alias`)
            SQL,
            static::$lastQueries[array_key_last(static::$lastQueries)]
        );
    }

    /**
     * @see  TableManager::getIndexes
     */
    public function testGetIndexes(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::exists
     */
    public function testExists(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getDatabase
     */
    public function testGetDatabase(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::truncate
     */
    public function testTruncate(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getColumn
     */
    public function testGetColumn(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::hasColumn
     */
    public function testHasColumn(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::hasIndex
     */
    public function testHasIndex(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getDataType
     */
    public function testGetDataType(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::drop
     */
    public function testDrop(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::rename
     */
    public function testRename(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::addColumn
     */
    public function testAddColumn(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::setDatabase
     */
    public function testSetDatabase(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getSchemaName
     */
    public function testGetSchema(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::reset
     */
    public function testReset(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @inheritDoc
     */
    protected static function setupDatabase(): void
    {
    }

    protected function setUp(): void
    {
        $this->instance = self::$db->getTable('ww_flower');
    }
}
