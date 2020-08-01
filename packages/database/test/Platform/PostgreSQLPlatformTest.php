<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Platform;

use Windwalker\Database\Platform\PostgreSQLPlatform;
use Windwalker\Database\Schema\PostgreSQLSchemaManager;

/**
 * The PostgreSQLPlatform class.
 */
class PostgreSQLPlatformTest extends AbstractPlatformTest
{
    protected static string $platform = 'PostgreSQL';

    protected static string $driver = 'pdo_pgsql';



    protected static $schema = 'public';

    /**
     * @var PostgreSQLPlatform
     */
    protected $instance;

    /**
     * @see  PostgreSQLSchemaManager::getDatabases()
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
     * @see  PostgreSQLSchemaManager::getSchemas
     */
    public function testGetSchemas(): void
    {
        $schemas = $this->instance->listSchemas();

        self::assertContains('public', $schemas);
    }

    /**
     * @see  PostgreSQLSchemaManager::getTables
     */
    public function testGetTables(): void
    {
        $tables = $this->instance->listTables(static::$schema);

        self::assertEquals(
            [
                'ww_articles' => [
                    'TABLE_NAME' => 'ww_articles',
                    'TABLE_CATALOG' => 'windwalker_test',
                    'TABLE_SCHEMA' => 'public',
                    'TABLE_TYPE' => 'BASE TABLE',
                    'view_definition' => null,
                    'check_option' => null,
                    'is_updatable' => null
                ],
                'ww_categories' => [
                    'TABLE_NAME' => 'ww_categories',
                    'TABLE_CATALOG' => 'windwalker_test',
                    'TABLE_SCHEMA' => 'public',
                    'TABLE_TYPE' => 'BASE TABLE',
                    'view_definition' => null,
                    'check_option' => null,
                    'is_updatable' => null
                ]
            ],
            $tables
        );
    }

    /**
     * @see  PostgreSQLSchemaManager::getViews
     */
    public function testGetViews(): void
    {
        $views = $this->instance->listViews(static::$schema);

        self::assertEquals(
            [
                'ww_articles_view' => [
                    'TABLE_NAME' => 'ww_articles_view',
                    'TABLE_CATALOG' => 'windwalker_test',
                    'TABLE_SCHEMA' => 'public',
                    'table_type' => 'VIEW',
                    'VIEW_DEFINITION' => ' SELECT ww_articles.id,
    ww_articles.category_id,
    ww_articles.page_id,
    ww_articles.type,
    ww_articles.price,
    ww_articles.title,
    ww_articles.alias,
    ww_articles.introtext,
    ww_articles.state,
    ww_articles.ordering,
    ww_articles.created,
    ww_articles.created_by,
    ww_articles.language,
    ww_articles.params
   FROM ww_articles;',
                    'CHECK_OPTION' => 'NONE',
                    'IS_UPDATABLE' => 'YES'
                ]
            ],
            $views
        );
    }

    /**
     * @see  PostgreSQLSchemaManager::getColumns
     */
    public function testGetColumns(): void
    {
        $columns = $this->instance->listColumns('#__articles', static::$schema);

        self::assertEquals(
            [
                'ordinal_position',
                'column_default',
                'is_nullable',
                'data_type',
                'character_maximum_length',
                'character_octet_length',
                'numeric_precision',
                'numeric_scale',
                'numeric_unsigned',
                'comment',
                'auto_increment',
                'erratas'
            ],
            array_keys($columns[array_key_first($columns)])
        );

        self::assertEquals(
            [
                'id' => [
                    'ordinal_position' => 1,
                    'column_default' => 0,
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 32,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'auto_increment' => true,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'category_id' => [
                    'ordinal_position' => 2,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 32,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'page_id' => [
                    'ordinal_position' => 3,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 32,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'type' => [
                    'ordinal_position' => 4,
                    'column_default' => 'bar',
                    'is_nullable' => false,
                    'data_type' => 'character',
                    'character_maximum_length' => 15,
                    'character_octet_length' => 60,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'price' => [
                    'ordinal_position' => 5,
                    'column_default' => '0.0',
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 20,
                    'numeric_scale' => 6,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
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
                    'auto_increment' => false,
                    'comment' => '',
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
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'introtext' => [
                    'ordinal_position' => 8,
                    'column_default' => null,
                    'is_nullable' => false,
                    'data_type' => 'text',
                    'character_maximum_length' => null,
                    'character_octet_length' => 1073741824,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'state' => [
                    'ordinal_position' => 9,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 32,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'ordering' => [
                    'ordinal_position' => 10,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 32,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'created' => [
                    'ordinal_position' => 11,
                    'column_default' => '1000-01-01 00:00:00',
                    'is_nullable' => false,
                    'data_type' => 'timestamp without time zone',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'created_by' => [
                    'ordinal_position' => 12,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 32,
                    'numeric_scale' => 0,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'language' => [
                    'ordinal_position' => 13,
                    'column_default' => '',
                    'is_nullable' => false,
                    'data_type' => 'character',
                    'character_maximum_length' => 7,
                    'character_octet_length' => 28,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ],
                'params' => [
                    'ordinal_position' => 14,
                    'column_default' => null,
                    'is_nullable' => false,
                    'data_type' => 'text',
                    'character_maximum_length' => null,
                    'character_octet_length' => 1073741824,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'auto_increment' => false,
                    'comment' => '',
                    'erratas' => [

                    ]
                ]
            ],
            $columns
        );
    }

    /**
     * @see  PostgreSQLSchemaManager::getConstraints
     */
    public function testGetConstraints(): void
    {
        $constraints = $this->instance->listConstraints('#__articles', static::$schema);

        $constraints = array_filter($constraints, function (array $constraint) {
            return $constraint['constraint_type'] !== 'CHECK';
        });

        self::assertEquals(
            [
                'ww_articles_pkey' => [
                    'constraint_name' => 'ww_articles_pkey',
                    'constraint_type' => 'PRIMARY KEY',
                    'table_name' => 'ww_articles',
                    'columns' => [
                        'id'
                    ]
                ],
                'fk_articles_category_id' => [
                    'constraint_name' => 'fk_articles_category_id',
                    'constraint_type' => 'FOREIGN KEY',
                    'table_name' => 'ww_articles',
                    'columns' => [
                        'category_id'
                    ],
                    'referenced_table_schema' => 'public',
                    'referenced_table_name' => 'ww_categories',
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
                    'table_name' => 'ww_articles',
                    'columns' => [
                        'page_id',
                        'created_by'
                    ],
                    'referenced_table_schema' => null,
                    'referenced_table_name' => null,
                    'referenced_columns' => [
                        null,
                        null
                    ],
                    'match_option' => 'NONE',
                    'update_rule' => 'RESTRICT',
                    'delete_rule' => 'RESTRICT'
                ],
                'idx_articles_alias' => [
                    'constraint_name' => 'idx_articles_alias',
                    'constraint_type' => 'UNIQUE',
                    'table_name' => 'ww_articles',
                    'columns' => [
                        'alias'
                    ]
                ],
            ],
            $constraints
        );
    }

    public function testGetIndexes(): void
    {
        $indexes = $this->instance->listIndexes('#__articles', static::$schema);

        self::assertEquals(
            [
                'ww_articles_pkey' => [
                    'table_schema' => 'public',
                    'table_name' => 'ww_articles',
                    'is_unique' => true,
                    'is_primary' => true,
                    'index_name' => 'ww_articles_pkey',
                    'index_comment' => '',
                    'columns' => [
                        'id' => [
                            'column_name' => 'id',
                            'sub_part' => null
                        ]
                    ]
                ],
                'idx_articles_category_id' => [
                    'table_schema' => 'public',
                    'table_name' => 'ww_articles',
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
                'idx_articles_alias' => [
                    'table_schema' => 'public',
                    'table_name' => 'ww_articles',
                    'is_unique' => true,
                    'is_primary' => false,
                    'index_name' => 'idx_articles_alias',
                    'index_comment' => '',
                    'columns' => [
                        'alias' => [
                            'column_name' => 'alias',
                            'sub_part' => null
                        ]
                    ]
                ],
                'idx_articles_created_by' => [
                    'table_schema' => 'public',
                    'table_name' => 'ww_articles',
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
                    'table_schema' => 'public',
                    'table_name' => 'ww_articles',
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
                    'table_schema' => 'public',
                    'table_name' => 'ww_articles',
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
        $this->instance = static::$db->getDriver()->getPlatform();
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
