<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Platform;

use Windwalker\Database\Platform\MysqlPlatform;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The MysqlPlatformTest class.
 */
class MysqlPlatformTest extends AbstractDatabaseTestCase
{
    /**
     * @var MysqlPlatform
     */
    protected $instance;

    /**
     * @see  MysqlPlatform::getSchemas
     */
    public function testGetSchemas(): void
    {
        $schemas = $this->instance->getSchemas();

        self::assertContains(
            self::getTestParams()['database'],
            $schemas
        );
    }

    /**
     * @see  MysqlPlatform::getTables
     */
    public function testGetTables(): void
    {
        $tables = $this->instance->getTables(static::$dbname);

        self::assertEquals(
            ['articles', 'categories'],
            $tables
        );
    }

    /**
     * @see  MysqlPlatform::getViews
     */
    public function testGetViews(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  MysqlPlatform::getColumns
     */
    public function testGetColumns(): void
    {
        $columns = $this->instance->getColumns('articles', static::$dbname);

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
                    'erratas' => [

                    ]
                ]
            ],
            $columns
        );
    }

    /**
     * @see  MysqlPlatform::getConstraints
     */
    public function testGetConstraints(): void
    {
        $constraints = $this->instance->getConstraints('articles', static::$dbname);

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

    public function testGetIndexes(): void
    {
        $indexes = $this->instance->getIndexes('articles', static::$dbname);

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

    // /**
    //  * @see  MysqlPlatform::getConstraintKeys
    //  */
    // public function testGetConstraintKeys(): void
    // {
    //     self::markTestIncomplete(); // TODO: Complete this test
    // }

    // /**
    //  * @see  MysqlPlatform::getTriggerNames
    //  */
    // public function testGetTriggerNames(): void
    // {
    //     self::markTestIncomplete(); // TODO: Complete this test
    // }
    //
    // /**
    //  * @see  MysqlPlatform::getTriggers
    //  */
    // public function testGetTriggers(): void
    // {
    //     self::markTestIncomplete(); // TODO: Complete this test
    // }

    protected function setUp(): void
    {
        $this->instance = static::$db->getPlatform();
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
