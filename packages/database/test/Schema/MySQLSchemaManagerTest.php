<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Schema;

use PHPUnit\Framework\TestCase;
use Windwalker\Database\Schema\MySQLSchemaManager;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The MySQLSchemaTest class.
 */
class MySQLSchemaManagerTest extends AbstractDatabaseTestCase
{
    /**
     * @var MySQLSchemaManager
     */
    protected $instance;

    /**
     * Will be set at setUp()
     *
     * @var string
     */
    protected static $schema = '';

    /**
     * @see  AbstractSchemaManager::listDatabases
     */
    public function testListDatabases(): void
    {
        $schemas = $this->instance->listDatabases();

        self::assertContains(
            self::getTestParams()['database'],
            $schemas
        );
    }

    /**
     * @see  AbstractSchemaManager::listSchemas
     */
    public function testListSchemas(): void
    {
        $schemas = $this->instance->listSchemas();

        self::assertContains(
            self::getTestParams()['database'],
            $schemas
        );
    }

    /**
     * @see  AbstractSchemaManager::listTables
     */
    public function testListTables(): void
    {
        $tables = $this->instance->listTables(static::$schema);

        self::assertEquals(
            ['articles', 'categories'],
            $tables
        );
    }

    /**
     * @see  AbstractSchemaManager::listViews
     */
    public function testListViews(): void
    {
        $views = $this->instance->listViews(static::$schema);

        self::assertEquals(
            ['articles_view'],
            $views
        );
    }

    /**
     * @see  AbstractSchemaManager::listColumns
     */
    public function testListColumns(): void
    {
        $columns = $this->instance->listColumns('articles', static::$schema);

        self::assertEquals(
            [
                'id' => [
                    'ordinal_position' => 1,
                    'column_default' => null,
                    'is_nullable' => false,
                    'data_type' => 'int',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 10,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => true,
                    'comment' => 'Primary Key',
                    'auto_increment' => true,
                    'erratas' => [

                    ]
                ],
                'category_id' => [
                    'ordinal_position' => 2,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'int',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 10,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => true,
                    'comment' => 'Category ID',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'page_id' => [
                    'ordinal_position' => 3,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'int',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 10,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => true,
                    'comment' => 'Page ID',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'type' => [
                    'ordinal_position' => 4,
                    'column_default' => 'bar',
                    'is_nullable' => false,
                    'data_type' => 'enum',
                    'character_maximum_length' => 3,
                    'character_octet_length' => 12,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [
                        'permitted_values' => [
                            'foo',
                            'bar',
                            'yoo'
                        ]
                    ]
                ],
                'price' => [
                    'ordinal_position' => 5,
                    'column_default' => '0.000000',
                    'is_nullable' => true,
                    'data_type' => 'decimal',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 20,
                    'numeric_scale' => 6,
                    'numeric_unsigned' => true,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'title' => [
                    'ordinal_position' => 6,
                    'column_default' => '',
                    'is_nullable' => false,
                    'data_type' => 'varchar',
                    'character_maximum_length' => 255,
                    'character_octet_length' => 1020,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => 'Title',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'alias' => [
                    'ordinal_position' => 7,
                    'column_default' => '',
                    'is_nullable' => false,
                    'data_type' => 'varchar',
                    'character_maximum_length' => 255,
                    'character_octet_length' => 1020,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => 'Alias',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'introtext' => [
                    'ordinal_position' => 8,
                    'column_default' => null,
                    'is_nullable' => false,
                    'data_type' => 'longtext',
                    'character_maximum_length' => 4294967295,
                    'character_octet_length' => 4294967295,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => 'Intro Text',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'state' => [
                    'ordinal_position' => 9,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'tinyint',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 3,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '0: unpublished, 1:published',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'ordering' => [
                    'ordinal_position' => 10,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'int',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 10,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => true,
                    'comment' => 'Ordering',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'created' => [
                    'ordinal_position' => 11,
                    'column_default' => '1000-01-01 00:00:00',
                    'is_nullable' => false,
                    'data_type' => 'datetime',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => 'Created Date',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'created_by' => [
                    'ordinal_position' => 12,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'int',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 10,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => true,
                    'comment' => 'Author',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'language' => [
                    'ordinal_position' => 13,
                    'column_default' => '',
                    'is_nullable' => false,
                    'data_type' => 'char',
                    'character_maximum_length' => 7,
                    'character_octet_length' => 28,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => 'Language',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'params' => [
                    'ordinal_position' => 14,
                    'column_default' => null,
                    'is_nullable' => false,
                    'data_type' => 'text',
                    'character_maximum_length' => 65535,
                    'character_octet_length' => 65535,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => 'Params',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ]
            ],
            $columns
        );
    }

    /**
     * @see  AbstractSchemaManager::listConstraints
     */
    public function testListConstraints(): void
    {
        $constraints = $this->instance->listConstraints('articles', static::$schema);

        self::assertEquals(
            [
                'PRIMARY' => [
                    'constraint_name' => 'PRIMARY',
                    'constraint_type' => 'PRIMARY KEY',
                    'table_name' => 'articles',
                    'columns' => [
                        'id'
                    ]
                ],
                'idx_articles_alias' => [
                    'constraint_name' => 'idx_articles_alias',
                    'constraint_type' => 'UNIQUE',
                    'table_name' => 'articles',
                    'columns' => [
                        'alias'
                    ]
                ],
                'fk_articles_category_id' => [
                    'constraint_name' => 'fk_articles_category_id',
                    'constraint_type' => 'FOREIGN KEY',
                    'table_name' => 'articles',
                    'columns' => [
                        'category_id'
                    ],
                    'referenced_table_schema' => 'windwalker_test',
                    'referenced_table_name' => 'categories',
                    'referenced_columns' => [
                        'id'
                    ],
                    'match_option' => 'NONE',
                    'update_rule' => 'RESTRICT',
                    'delete_rule' => 'RESTRICT'
                ],
                'fk_articles_category_more' => [
                    'constraint_name' => 'fk_articles_category_more',
                    'constraint_type' => 'FOREIGN KEY',
                    'table_name' => 'articles',
                    'columns' => [
                        'page_id',
                        'created_by'
                    ],
                    'referenced_table_schema' => 'windwalker_test',
                    'referenced_table_name' => 'categories',
                    'referenced_columns' => [
                        'parent_id',
                        'level'
                    ],
                    'match_option' => 'NONE',
                    'update_rule' => 'RESTRICT',
                    'delete_rule' => 'RESTRICT'
                ]
            ],
            $constraints
        );
    }

    /**
     * @see  AbstractSchemaManager::listIndexes
     */
    public function testListIndexes(): void
    {
        $indexes = $this->instance->listIndexes('articles', static::$schema);

        self::assertEquals(
            [
                'PRIMARY' => [
                    'table_schema' => 'windwalker_test',
                    'table_name' => 'articles',
                    'is_unique' => true,
                    'is_primary' => true,
                    'index_name' => 'PRIMARY',
                    'index_comment' => '',
                    'columns' => [
                        'id' => [
                            'column_name' => 'id',
                            'sub_part' => null
                        ]
                    ]
                ],
                'idx_articles_alias' => [
                    'table_schema' => 'windwalker_test',
                    'table_name' => 'articles',
                    'is_unique' => true,
                    'is_primary' => false,
                    'index_name' => 'idx_articles_alias',
                    'index_comment' => '',
                    'columns' => [
                        'alias' => [
                            'column_name' => 'alias',
                            'sub_part' => 150
                        ]
                    ]
                ],
                'fk_articles_category_more' => [
                    'table_schema' => 'windwalker_test',
                    'table_name' => 'articles',
                    'is_unique' => false,
                    'is_primary' => false,
                    'index_name' => 'fk_articles_category_more',
                    'index_comment' => '',
                    'columns' => [
                        'page_id' => [
                            'column_name' => 'page_id',
                            'sub_part' => null
                        ],
                        'created_by' => [
                            'column_name' => 'created_by',
                            'sub_part' => null
                        ]
                    ]
                ],
                'idx_articles_category_id' => [
                    'table_schema' => 'windwalker_test',
                    'table_name' => 'articles',
                    'is_unique' => false,
                    'is_primary' => false,
                    'index_name' => 'idx_articles_category_id',
                    'index_comment' => '',
                    'columns' => [
                        'category_id' => [
                            'column_name' => 'category_id',
                            'sub_part' => null
                        ]
                    ]
                ],
                'idx_articles_created_by' => [
                    'table_schema' => 'windwalker_test',
                    'table_name' => 'articles',
                    'is_unique' => false,
                    'is_primary' => false,
                    'index_name' => 'idx_articles_created_by',
                    'index_comment' => '',
                    'columns' => [
                        'created_by' => [
                            'column_name' => 'created_by',
                            'sub_part' => null
                        ]
                    ]
                ],
                'idx_articles_language' => [
                    'table_schema' => 'windwalker_test',
                    'table_name' => 'articles',
                    'is_unique' => false,
                    'is_primary' => false,
                    'index_name' => 'idx_articles_language',
                    'index_comment' => '',
                    'columns' => [
                        'language' => [
                            'column_name' => 'language',
                            'sub_part' => null
                        ]
                    ]
                ],
                'idx_articles_page_id' => [
                    'table_schema' => 'windwalker_test',
                    'table_name' => 'articles',
                    'is_unique' => false,
                    'is_primary' => false,
                    'index_name' => 'idx_articles_page_id',
                    'index_comment' => '',
                    'columns' => [
                        'page_id' => [
                            'column_name' => 'page_id',
                            'sub_part' => null
                        ]
                    ]
                ]
            ],
            $indexes
        );
    }

    protected function setUp(): void
    {
        $this->instance = static::$db->getDriver()->getSchemaManager();

        static::$schema = static::$dbname;
    }

    protected function tearDown(): void
    {
    }

    /**
     * @inheritDoc
     */
    protected static function setupDatabase(): void
    {
        self::importFromFile(__DIR__ . '/../stub/metadata/' . static::$platform . '.sql');
    }
}
