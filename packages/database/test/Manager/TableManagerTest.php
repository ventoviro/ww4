<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Manager;

use Windwalker\Database\Manager\TableManager;
use PHPUnit\Framework\TestCase;
use Windwalker\Database\Schema\Schema;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

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
        self::$db->getTable('flower')
            ->create(
                function (Schema $schema) {
                    $schema->primary('id');
                    $schema->integer('catid')->isNullable(true);
                    $schema->varchar('title')->defaultValue('H');
                    $schema->decimal('price')->length('20,6');
                    $schema->text('intro');
                }
            );

        exit(' @Checkpoint');
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
     * @see  TableManager::getSchema
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
