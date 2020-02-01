<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Platform;

use Windwalker\Database\Platform\SQLServerPlatform;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The SQLServerPlatformTest class.
 */
class SQLServerPlatformTest extends AbstractDatabaseTestCase
{
    protected static $platform = 'SQLServer';

    protected static $driver = 'pdo_sqlsrv';

    protected static $schema = 'dbo';

    /**
     * @var SQLServerPlatform
     */
    protected $instance;

    /**
     * @see  SQLServerPlatform::getDatabases()
     */
    public function testGetDatabases(): void
    {
        $databases = $this->instance->getDatabases();

        self::assertContains(
            self::getTestParams()['database'],
            $databases
        );
    }

    /**
     * @see  SQLServerPlatform::getSchemas
     */
    public function testGetSchemas(): void
    {
        $schemas = $this->instance->getSchemas();

        $defaults = [
            'pg_catalog',
            'public',
            'information_schema',
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
     * @see  SQLServerPlatform::getTables
     */
    public function testGetTables(): void
    {
        $tables = $this->instance->getTables(static::$schema);

        self::assertEquals(
            ['articles', 'categories'],
            $tables
        );
    }

    /**
     * @see  SQLServerPlatform::getViews
     */
    public function testGetViews(): void
    {
        $views = $this->instance->getViews(static::$schema);

        self::assertEquals(
            ['articles_view'],
            $views
        );
    }

    /**
     * @see  SQLServerPlatform::getColumns
     */
    public function testGetColumns(): void
    {
        $columns = $this->instance->getColumns('articles', static::$schema);

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
     * @see  SQLServerPlatform::getConstraints
     */
    public function testGetConstraints(): void
    {
        $constraints = $this->instance->getConstraints('articles', static::$schema);

        $constraints = array_filter($constraints, function (array $constraint) {
            return $constraint['constraint_type'] !== 'CHECK';
        });

        self::assertEquals(
            [
                'articles_pkey' => [
                    'constraint_name' => 'articles_pkey',
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
                    'referenced_table_schema' => 'public',
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
                    'referenced_table_schema' => null,
                    'referenced_table_name' => null,
                    'referenced_columns' => [
                        null,
                        null
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
        $indexes = $this->instance->getIndexes('articles', static::$schema);

        self::assertEquals(
            [
                'articles_pkey' => [
                    'table_schema' => 'public',
                    'table_name' => 'articles',
                    'is_unique' => true,
                    'is_primary' => true,
                    'index_name' => 'articles_pkey',
                    'index_comment' => '',
                    'columns' => [
                        'id' => [
                            'column_name' => 'id',
                            'sub_part' => null
                        ]
                    ]
                ],
                'idx_articles_alias' => [
                    'table_schema' => 'public',
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
                    'table_schema' => 'public',
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
                    'table_schema' => 'public',
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
                    'table_schema' => 'public',
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
                    'table_schema' => 'public',
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
    //  * @see  SQLServerPlatform::getConstraintKeys
    //  */
    // public function testGetConstraintKeys(): void
    // {
    //     self::markTestIncomplete(); // TODO: Complete this test
    // }

    // /**
    //  * @see  SQLServerPlatform::getTriggerNames
    //  */
    // public function testGetTriggerNames(): void
    // {
    //     self::markTestIncomplete(); // TODO: Complete this test
    // }
    //
    // /**
    //  * @see  SQLServerPlatform::getTriggers
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
