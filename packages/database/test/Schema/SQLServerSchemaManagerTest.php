<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Schema;

use Windwalker\Database\Schema\SQLServerSchemaManager;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The SQLServerSchemaManagerTest class.
 */
class SQLServerSchemaManagerTest extends AbstractDatabaseTestCase
{
    protected static $platform = 'SQLServer';

    protected static $driver = 'pdo_sqlsrv';

    protected static $schema = 'dbo';

    /**
     * @var SQLServerSchemaManager
     */
    protected $instance;

    /**
     * @see  SQLServerSchemaManager::getDatabases()
     */
    public function testGetDatabases(): void
    {
        $databases = $this->instance->listDatabases();

        self::assertContains(
            self::getTestParams()['database'],
            $databases
        );
    }

    /**
     * @see  SQLServerSchemaManager::getSchemas
     */
    public function testGetSchemas(): void
    {
        $schemas = $this->instance->listSchemas();

        $defaults = [
            'dbo',
            'guest',
            'sys',
        ];

        self::assertEquals(
            $defaults,
            array_values(
                array_intersect(
                    $schemas,
                    $defaults
                )
            )
        );
    }

    /**
     * @see  SQLServerSchemaManager::getTables
     */
    public function testGetTables(): void
    {
        $tables = $this->instance->listTables(static::$schema);

        self::assertEquals(
            ['articles', 'categories'],
            $tables
        );
    }

    /**
     * @see  SQLServerSchemaManager::getViews
     */
    public function testGetViews(): void
    {
        $views = $this->instance->listViews(static::$schema);

        self::assertEquals(
            ['articles_view'],
            $views
        );
    }

    /**
     * @see  SQLServerSchemaManager::getColumns
     */
    public function testGetColumns(): void
    {
        $columns = $this->instance->listColumns('articles', static::$schema);

        self::assertEquals(
            [
                'id' => [
                    'ordinal_position' => 1,
                    'column_default' => '',
                    'is_nullable' => false,
                    'data_type' => 'int',
                    'character_maximum_length' => 0,
                    'character_octet_length' => 0,
                    'numeric_precision' => 10,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => true,
                    'erratas' => [

                    ]
                ],
                'category_id' => [
                    'ordinal_position' => 2,
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'int',
                    'character_maximum_length' => 0,
                    'character_octet_length' => 0,
                    'numeric_precision' => 10,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'page_id' => [
                    'ordinal_position' => 3,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'int',
                    'character_maximum_length' => 0,
                    'character_octet_length' => 0,
                    'numeric_precision' => 10,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'type' => [
                    'ordinal_position' => 4,
                    'column_default' => 'bar',
                    'is_nullable' => false,
                    'data_type' => 'char',
                    'character_maximum_length' => 15,
                    'character_octet_length' => 15,
                    'numeric_precision' => 0,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'price' => [
                    'ordinal_position' => 5,
                    'column_default' => '0.0',
                    'is_nullable' => true,
                    'data_type' => 'decimal',
                    'character_maximum_length' => 0,
                    'character_octet_length' => 0,
                    'numeric_precision' => 20,
                    'numeric_scale' => 6,
                    'numeric_unsigned' => false,
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
                    'character_octet_length' => 255,
                    'numeric_precision' => 0,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
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
                    'character_octet_length' => 255,
                    'numeric_precision' => 0,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'introtext' => [
                    'ordinal_position' => 8,
                    'column_default' => '',
                    'is_nullable' => false,
                    'data_type' => 'varchar',
                    'character_maximum_length' => -1,
                    'character_octet_length' => -1,
                    'numeric_precision' => 0,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'state' => [
                    'ordinal_position' => 9,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'tinyint',
                    'character_maximum_length' => 0,
                    'character_octet_length' => 0,
                    'numeric_precision' => 3,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'ordering' => [
                    'ordinal_position' => 10,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'int',
                    'character_maximum_length' => 0,
                    'character_octet_length' => 0,
                    'numeric_precision' => 10,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'created' => [
                    'ordinal_position' => 11,
                    'column_default' => '1000-01-01 00:00:00',
                    'is_nullable' => false,
                    'data_type' => 'datetime',
                    'character_maximum_length' => 0,
                    'character_octet_length' => 0,
                    'numeric_precision' => 0,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'created_by' => [
                    'ordinal_position' => 12,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'int',
                    'character_maximum_length' => 0,
                    'character_octet_length' => 0,
                    'numeric_precision' => 10,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
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
                    'character_octet_length' => 7,
                    'numeric_precision' => 0,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ],
                'params' => [
                    'ordinal_position' => 14,
                    'column_default' => '',
                    'is_nullable' => false,
                    'data_type' => 'text',
                    'character_maximum_length' => 2147483647,
                    'character_octet_length' => 2147483647,
                    'numeric_precision' => 0,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'comment' => '',
                    'auto_increment' => false,
                    'erratas' => [

                    ]
                ]
            ],
            $columns
        );
    }

    /**
     * @see  SQLServerSchemaManager::getConstraints
     */
    public function testGetConstraints(): void
    {
        $constraints = $this->instance->listConstraints('articles', static::$schema);

        self::assertEquals(
            [
                'PK__articles' => [
                    'constraint_name' => 'PK__articles',
                    'constraint_type' => 'PRIMARY KEY',
                    'table_name' => 'articles',
                    'columns' => [
                        'id'
                    ]
                ],
                'fk_articles_category_id' => [
                    'constraint_name' => 'fk_articles_category_id',
                    'constraint_type' => 'FOREIGN KEY',
                    'table_name' => 'articles',
                    'columns' => [
                        'category_id'
                    ],
                    'referenced_table_schema' => 'dbo',
                    'referenced_table_name' => 'categories',
                    'referenced_columns' => [
                        'id'
                    ],
                    'match_option' => 'SIMPLE',
                    'update_rule' => 'SET NULL',
                    'delete_rule' => 'SET NULL'
                ],
                'fk_articles_category_more' => [
                    'constraint_name' => 'fk_articles_category_more',
                    'constraint_type' => 'FOREIGN KEY',
                    'table_name' => 'articles',
                    'columns' => [
                        'page_id',
                        'created_by'
                    ],
                    'referenced_table_schema' => 'dbo',
                    'referenced_table_name' => 'categories',
                    'referenced_columns' => [
                        'parent_id',
                        'level'
                    ],
                    'match_option' => 'SIMPLE',
                    'update_rule' => 'NO ACTION',
                    'delete_rule' => 'NO ACTION'
                ]
            ],
            $constraints
        );
    }

    public function testGetIndexes(): void
    {
        $indexes = $this->instance->listIndexes('articles', static::$schema);

        self::assertEquals(
            [
                'PK__articles' => [
                    'table_schema' => 'dbo',
                    'table_name' => 'articles',
                    'is_unique' => true,
                    'is_primary' => true,
                    'index_name' => $indexes['PK__articles']['index_name'],
                    'index_comment' => '',
                    'columns' => [
                        'id' => [
                            'column_name' => 'id',
                            'sub_part' => null
                        ]
                    ]
                ],
                'idx_articles_alias' => [
                    'table_schema' => 'dbo',
                    'table_name' => 'articles',
                    'is_unique' => true,
                    'is_primary' => false,
                    'index_name' => 'idx_articles_alias',
                    'index_comment' => '',
                    'columns' => [
                        'type' => [
                            'column_name' => 'type',
                            'sub_part' => null
                        ],
                        'alias' => [
                            'column_name' => 'alias',
                            'sub_part' => null
                        ]
                    ]
                ],
                'idx_articles_category_id' => [
                    'table_schema' => 'dbo',
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
                    'table_schema' => 'dbo',
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
                    'table_schema' => 'dbo',
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
                    'table_schema' => 'dbo',
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
