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
use Windwalker\Database\Schema\Ddl\Constraint;
use Windwalker\Database\Schema\Schema;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

class TableManagerTest extends AbstractDatabaseTestCase
{
    protected ?TableManager $instance;

    /**
     * @see  TableManager::create
     */
    public function testCreate(): void
    {
        $table = self::$db->getTable('enterprise');

        $logs = $this->logQueries(
            fn () => $table->create(
                static function (Schema $schema) {
                    $schema->primary('id');
                    $schema->char('type')->length(25);
                    $schema->integer('catid')->nullable(true);
                    $schema->varchar('alias');
                    $schema->varchar('title')->defaultValue('H');
                    $schema->decimal('price')->length('20,6');
                    $schema->text('intro');
                    $schema->text('fulltext');
                    $schema->datetime('start_date');
                    $schema->datetime('created');
                    $schema->timestamp('updated')
                        ->onUpdateCurrent()
                        ->defaultCurrent();
                    $schema->timestamp('deleted');
                    $schema->json('params');

                    $schema->addIndex(['catid', 'type']);
                    $schema->addIndex('title(150)');
                    $schema->addUniqueKey('alias');
                }
            )
        );

        self::assertSqlFormatEquals(
            <<<SQL
            CREATE TABLE IF NOT EXISTS `enterprise` (
            `id` int(11) NOT NULL,
            `type` char(25) NOT NULL DEFAULT '',
            `catid` int(11) DEFAULT NULL,
            `alias` varchar(255) NOT NULL DEFAULT '',
            `title` varchar(255) NOT NULL DEFAULT 'H',
            `price` decimal(20,6) NOT NULL DEFAULT 0,
            `intro` text NOT NULL,
            `fulltext` text NOT NULL,
            `start_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
            `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
            `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `deleted` timestamp NOT NULL DEFAULT '1970-01-01 12:00:01',
            `params` json NOT NULL
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ALTER TABLE `enterprise` ADD CONSTRAINT PRIMARY KEY (`id`);
            ALTER TABLE `enterprise` MODIFY COLUMN `id` int(11) NOT NULL AUTO_INCREMENT;
            ALTER TABLE `enterprise` ADD INDEX `idx_enterprise_catid_type` (`catid`,`type`);
            ALTER TABLE `enterprise` ADD INDEX `idx_enterprise_title` (`title`(150));
            ALTER TABLE `enterprise` ADD CONSTRAINT `idx_enterprise_alias` UNIQUE (`alias`)
            SQL,
            implode(";\n", $logs)
        );

        self::assertArrayHasKey('enterprise', $table->getPlatform()->listTables());
    }

    /**
     * @see  TableManager::getSchema
     */
    public function testGetSchema(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getConstraints
     */
    public function testGetConstraints(): void
    {
        $constraints = $this->instance->getConstraints();

        self::assertEquals(
            ['enterprise_PRIMARY', 'enterprise_idx_enterprise_alias', 'enterprise_params'],
            array_keys($constraints)
        );
    }

    /**
     * @see  TableManager::getDetail
     */
    public function testGetDetail(): void
    {
        $detail = $this->instance->getDetail();

        self::assertEquals(
            [
                'TABLE_NAME' => 'enterprise',
                'TABLE_SCHEMA' => 'windwalker_test',
                'TABLE_TYPE' => 'BASE TABLE',
                'VIEW_DEFINITION' => null,
                'CHECK_OPTION' => null,
                'IS_UPDATABLE' => null
            ],
            $detail
        );
    }

    /**
     * @see  TableManager::update
     */
    public function testUpdate(): void
    {
        $logs = $this->logQueries(
            fn () => $this->instance->update(function (Schema $schema) {
                // New column
                $schema->varchar('captain')->length(512)->after('catid');
                $schema->varchar('first_officer')->length(512)->after('captain');

                // Update column
                $schema->char('alias')->length(25)
                    ->nullable(true)
                    ->defaultValue('');

                // New index
                $schema->addIndex('captain');
            })
        );

        self::assertSqlFormatEquals(
            <<<SQL
            SELECT `ORDINAL_POSITION`,
                   `COLUMN_DEFAULT`,
                   `IS_NULLABLE`,
                   `DATA_TYPE`,
                   `CHARACTER_MAXIMUM_LENGTH`,
                   `CHARACTER_OCTET_LENGTH`,
                   `NUMERIC_PRECISION`,
                   `NUMERIC_SCALE`,
                   `COLUMN_NAME`,
                   `COLUMN_TYPE`,
                   `COLUMN_COMMENT`,
                   `EXTRA`
            FROM `INFORMATION_SCHEMA`.`COLUMNS`
            WHERE `TABLE_NAME` = 'enterprise'
              AND `TABLE_SCHEMA` = (SELECT DATABASE());
            ALTER TABLE `enterprise`
                ADD COLUMN `captain` varchar(512) NOT NULL;
            ALTER TABLE `enterprise`
                ADD COLUMN `first_officer` varchar(512) NOT NULL;
            ALTER TABLE `enterprise`
                MODIFY COLUMN `alias` char(25) DEFAULT '';
            SELECT `TABLE_SCHEMA`,
                   `TABLE_NAME`,
                   `NON_UNIQUE`,
                   `INDEX_NAME`,
                   `COLUMN_NAME`,
                   `COLLATION`,
                   `CARDINALITY`,
                   `SUB_PART`,
                   `INDEX_COMMENT`
            FROM `INFORMATION_SCHEMA`.`STATISTICS`
            WHERE `TABLE_NAME` = 'enterprise'
              AND `TABLE_SCHEMA` = (SELECT DATABASE());
            ALTER TABLE `enterprise`
                ADD INDEX `idx_enterprise_captain` (`captain`)
            SQL,
            implode("\n;", $logs)
        );
    }

    /**
     * @see  TableManager::addIndex
     */
    public function testAddIndex(): void
    {
        $logs = $this->logQueries(
            function () {
                $this->instance->addIndex('created');
                $this->instance->addIndex(['start_date', 'params']);
            }
        );

        self::assertSqlFormatEquals(
            <<<SQL
            SELECT `ORDINAL_POSITION`,
                   `COLUMN_DEFAULT`,
                   `IS_NULLABLE`,
                   `DATA_TYPE`,
                   `CHARACTER_MAXIMUM_LENGTH`,
                   `CHARACTER_OCTET_LENGTH`,
                   `NUMERIC_PRECISION`,
                   `NUMERIC_SCALE`,
                   `COLUMN_NAME`,
                   `COLUMN_TYPE`,
                   `COLUMN_COMMENT`,
                   `EXTRA`
            FROM `INFORMATION_SCHEMA`.`COLUMNS`
            WHERE `TABLE_NAME` = 'enterprise'
              AND `TABLE_SCHEMA` = (SELECT DATABASE());
            SELECT `TABLE_SCHEMA`,
                   `TABLE_NAME`,
                   `NON_UNIQUE`,
                   `INDEX_NAME`,
                   `COLUMN_NAME`,
                   `COLLATION`,
                   `CARDINALITY`,
                   `SUB_PART`,
                   `INDEX_COMMENT`
            FROM `INFORMATION_SCHEMA`.`STATISTICS`
            WHERE `TABLE_NAME` = 'enterprise'
              AND `TABLE_SCHEMA` = (SELECT DATABASE());
            ALTER TABLE `enterprise`
                ADD INDEX `idx_enterprise_created` (`created`);
            ALTER TABLE `enterprise`
                ADD INDEX `idx_enterprise_start_date_params` (`start_date`, `params`(150))
            SQL,
            implode(";\n", $logs)
        );

        $this->instance->reset();

        self::assertEquals(
            [
                'enterprise_PRIMARY',
                'enterprise_idx_enterprise_alias',
                'enterprise_idx_enterprise_catid_type',
                'enterprise_idx_enterprise_title',
                'enterprise_idx_enterprise_captain',
                'enterprise_idx_enterprise_created',
                'enterprise_idx_enterprise_start_date_params',
            ],
            array_keys($this->instance->getIndexes())
        );
    }

    /**
     * @see  TableManager::hasConstraint
     */
    public function testHasConstraint(): void
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
     * @see  TableManager::hasIndex
     */
    public function testHasIndex(): void
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
     * @see  TableManager::addConstraint
     */
    public function testAddConstraint(): void
    {
        $logs = $this->logQueries(
            fn () => $this->instance->addConstraint(
                ['captain', 'first_officer'],
                Constraint::TYPE_UNIQUE
            )
        );

        self::assertSqlFormatEquals(
            <<<SQL
SELECT `ORDINAL_POSITION`,
       `COLUMN_DEFAULT`,
       `IS_NULLABLE`,
       `DATA_TYPE`,
       `CHARACTER_MAXIMUM_LENGTH`,
       `CHARACTER_OCTET_LENGTH`,
       `NUMERIC_PRECISION`,
       `NUMERIC_SCALE`,
       `COLUMN_NAME`,
       `COLUMN_TYPE`,
       `COLUMN_COMMENT`,
       `EXTRA`
FROM `INFORMATION_SCHEMA`.`COLUMNS`
WHERE `TABLE_NAME` = 'enterprise'
  AND `TABLE_SCHEMA` = (SELECT DATABASE());
SELECT `TABLE_NAME`, `CONSTRAINT_NAME`, `CONSTRAINT_TYPE`
FROM `INFORMATION_SCHEMA`.`TABLE_CONSTRAINTS`
WHERE `TABLE_NAME` = 'enterprise'
  AND `TABLE_SCHEMA` = (SELECT DATABASE());
SELECT `CONSTRAINT_NAME`,
       `COLUMN_NAME`,
       `REFERENCED_TABLE_SCHEMA`,
       `REFERENCED_TABLE_NAME`,
       `REFERENCED_COLUMN_NAME`,
       `REFERENCED_COLUMN_NAME`
FROM `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
WHERE `TABLE_NAME` = 'enterprise'
  AND `TABLE_SCHEMA` = (SELECT DATABASE());
SELECT `CONSTRAINT_NAME`, `MATCH_OPTION`, `UPDATE_RULE`, `DELETE_RULE`
FROM `INFORMATION_SCHEMA`.`REFERENTIAL_CONSTRAINTS`
WHERE `TABLE_NAME` = 'enterprise'
  AND `CONSTRAINT_SCHEMA` = (SELECT DATABASE());
ALTER TABLE `enterprise`
    ADD CONSTRAINT `ct_enterprise_captain_first_officer` UNIQUE (`captain`(150),`first_officer`(150))
SQL,
            implode(";\n", $logs)
        );
    }

    /**
     * @see  TableManager::dropColumn
     */
    public function testDropColumn(): void
    {
        $this->instance->reset();
        $this->instance->dropColumn(['captain', 'first_officer']);

        $this->instance->reset();

        self::assertFalse($this->instance->hasColumn('captain'));
        self::assertFalse($this->instance->hasColumn('first_officer'));
    }

    /**
     * @see  TableManager::modifyColumn
     */
    public function testModifyColumn(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::dropConstraint
     */
    public function testDropConstraint(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::save
     */
    public function testSave(): void
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
     * @see  TableManager::truncate
     */
    public function testTruncate(): void
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
     * @see  TableManager::setName
     */
    public function testSetName(): void
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
     * @see  TableManager::getColumnNames
     */
    public function testGetColumnNames(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getIndex
     */
    public function testGetIndex(): void
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
     * @see  TableManager::exists
     */
    public function testExists(): void
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
     * @see  TableManager::getConstraint
     */
    public function testGetConstraint(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  TableManager::getIndexes
     */
    public function testGetIndexes(): void
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
     * @see  TableManager::rename
     */
    public function testRename(): void
    {
        $newTable = $this->instance->rename('enterprise_d');

        self::assertNotSame($newTable, $this->instance);

        self::assertEquals('enterprise_d', $newTable->getName());

        self::assertTrue($newTable->exists());
        self::assertFalse($this->instance->exists());

        $this->instance = $newTable;
    }

    /**
     * @see  TableManager::drop
     */
    public function testDrop(): void
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
        $this->instance = self::$db->getTable('enterprise');
    }
}
