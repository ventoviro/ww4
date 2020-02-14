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
use Windwalker\Database\Schema\SQLiteSchemaManager;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The MySQLSchemaTest class.
 */
class SQLiteSchemaManagerTest extends AbstractDatabaseTestCase
{
    protected static $platform = 'SQLite';

    protected static $driver = 'pdo_sqlite';

    protected static $schema = 'main';

    /**
     * @var SQLiteSchemaManager
     */
    protected $instance;

    /**
     * @see  AbstractSchemaManager::listDatabases
     */
    public function testListDatabases(): void
    {
        $dbs = $this->instance->listDatabases();

        self::assertContains(
            'main',
            $dbs
        );
    }

    /**
     * @see  AbstractSchemaManager::listSchemas
     */
    public function testListSchemas(): void
    {
        $schemas = $this->instance->listSchemas();

        self::assertContains(
            'main',
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
            ['ww_articles', 'ww_categories'],
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
            ['ww_articles_view'],
            $views
        );
    }

    /**
     * @see  AbstractSchemaManager::listColumns
     */
    public function testListColumns(): void
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
                    'column_default' => null,
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => true,
                    'erratas' => [
                        'pk' => true
                    ]
                ],
                'category_id' => [
                    'ordinal_position' => 2,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'page_id' => [
                    'ordinal_position' => 3,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'type' => [
                    'ordinal_position' => 4,
                    'column_default' => 'bar',
                    'is_nullable' => false,
                    'data_type' => 'char',
                    'character_maximum_length' => 15,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'price' => [
                    'ordinal_position' => 5,
                    'column_default' => '0.0',
                    'is_nullable' => true,
                    'data_type' => 'decimal',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 20,
                    'numeric_scale' => 6,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'title' => [
                    'ordinal_position' => 6,
                    'column_default' => '',
                    'is_nullable' => false,
                    'data_type' => 'varchar',
                    'character_maximum_length' => 255,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'alias' => [
                    'ordinal_position' => 7,
                    'column_default' => '',
                    'is_nullable' => false,
                    'data_type' => 'varchar',
                    'character_maximum_length' => 255,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'introtext' => [
                    'ordinal_position' => 8,
                    'column_default' => null,
                    'is_nullable' => false,
                    'data_type' => 'longtext',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'state' => [
                    'ordinal_position' => 9,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'tinyint',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => 1,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'ordering' => [
                    'ordinal_position' => 10,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
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
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'created_by' => [
                    'ordinal_position' => 12,
                    'column_default' => '0',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'language' => [
                    'ordinal_position' => 13,
                    'column_default' => '',
                    'is_nullable' => false,
                    'data_type' => 'char',
                    'character_maximum_length' => 7,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
                    ]
                ],
                'params' => [
                    'ordinal_position' => 14,
                    'column_default' => null,
                    'is_nullable' => false,
                    'data_type' => 'text',
                    'character_maximum_length' => null,
                    'character_octet_length' => null,
                    'numeric_precision' => null,
                    'numeric_scale' => null,
                    'numeric_unsigned' => false,
                    'comment' => null,
                    'auto_increment' => false,
                    'erratas' => [
                        'pk' => false
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
        $constraints = $this->instance->listConstraints('#__articles', static::$schema);

        self::assertEquals(
            [
                'idx_articles_alias' => [
                    'constraint_name' => 'idx_articles_alias',
                    'constraint_type' => 'UNIQUE',
                    'table_name' => 'ww_articles',
                    'columns' => [
                        'alias'
                    ]
                ],
                'sqlite_autoindex_ww_articles_1' => [
                    'constraint_name' => 'sqlite_autoindex_ww_articles_1',
                    'constraint_type' => 'PRIMARY KEY',
                    'table_name' => 'ww_articles',
                    'columns' => [
                        'id'
                    ]
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
        $indexes = $this->instance->listIndexes('#__articles', static::$schema);

        self::assertEquals(
            [
                'idx_articles_page_id' => [
                    'table_schema' => 'main',
                    'table_name' => 'ww_articles',
                    'is_unique' => false,
                    'index_name' => 'idx_articles_page_id',
                    'index_comment' => '',
                    'columns' => [
                        'page_id' => [
                            'column_name' => 'page_id',
                            'subpart' => null
                        ]
                    ]
                ],
                'idx_articles_language' => [
                    'table_schema' => 'main',
                    'table_name' => 'ww_articles',
                    'is_unique' => false,
                    'index_name' => 'idx_articles_language',
                    'index_comment' => '',
                    'columns' => [
                        'language' => [
                            'column_name' => 'language',
                            'subpart' => null
                        ]
                    ]
                ],
                'idx_articles_created_by' => [
                    'table_schema' => 'main',
                    'table_name' => 'ww_articles',
                    'is_unique' => false,
                    'index_name' => 'idx_articles_created_by',
                    'index_comment' => '',
                    'columns' => [
                        'created_by' => [
                            'column_name' => 'created_by',
                            'subpart' => null
                        ]
                    ]
                ],
                'idx_articles_category_id' => [
                    'table_schema' => 'main',
                    'table_name' => 'ww_articles',
                    'is_unique' => false,
                    'index_name' => 'idx_articles_category_id',
                    'index_comment' => '',
                    'columns' => [
                        'category_id' => [
                            'column_name' => 'category_id',
                            'subpart' => null
                        ]
                    ]
                ],
                'idx_articles_alias' => [
                    'table_schema' => 'main',
                    'table_name' => 'ww_articles',
                    'is_unique' => true,
                    'index_name' => 'idx_articles_alias',
                    'index_comment' => '',
                    'columns' => [
                        'alias' => [
                            'column_name' => 'alias',
                            'subpart' => null
                        ]
                    ]
                ],
                'sqlite_autoindex_ww_articles_1' => [
                    'table_schema' => 'main',
                    'table_name' => 'ww_articles',
                    'is_unique' => true,
                    'index_name' => 'sqlite_autoindex_ww_articles_1',
                    'index_comment' => '',
                    'columns' => [
                        'id' => [
                            'column_name' => 'id',
                            'subpart' => null
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
