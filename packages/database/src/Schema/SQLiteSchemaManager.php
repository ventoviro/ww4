<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema;

use Windwalker\Database\Platform\SQLitePlatform;
use Windwalker\Query\Escaper;
use Windwalker\Utilities\TypeCast;

/**
 * The SQLiteSchemaManager class.
 */
class SQLiteSchemaManager extends AbstractSchemaManager
{
    /**
     * @inheritDoc
     */
    public function listDatabases(): array
    {
        return $this->db->prepare(
            $this->getPlatform()->listDatabasesQuery()
        )
            ->loadColumn(1)
            ->dump();
    }

    /**
     * @inheritDoc
     */
    public function listSchemas(): array
    {
        return $this->listDatabases();
    }

    /**
     * @inheritDoc
     */
    public function listColumns(string $table, ?string $schema = null): array
    {
        $columns = [];

        foreach ($this->loadColumnsStatement($table, $schema) as $row) {
            preg_match(
                '/(\w+)\(*(\d*)[,\s]*(\d*)\)*/',
                $row['type'],
                $matches
            );

            [, $type, $precision, $scale] = $matches;

            $isString = in_array(
                $type = strtolower($type),
                [
                    'char',
                    'varchar',
                    'text',
                    'mediumtext',
                    'longtext'
                ]
            );

            $columns[$row['name']] = [
                // cid appears to be zero-based, ordinal position needs to be one-based
                'ordinal_position'          => $row['cid'] + 1,
                'column_default'            => Escaper::stripQuoteIfExists($row['dflt_value']),
                'is_nullable'               => !$row['notnull'],
                'data_type'                 => $type,
                'character_maximum_length'  => $isString ? TypeCast::tryInteger($precision, true) : null,
                'character_octet_length'    => null,
                'numeric_precision'         => $isString ? null : TypeCast::tryInteger($precision, true),
                'numeric_scale'             => $isString ? null : TypeCast::tryInteger($scale, true),
                'numeric_unsigned'          => false,
                'comment'                   => null,
                'auto_increment'            => (bool) $row['pk'],
                'erratas'                   => [
                    'pk' => (bool) $row['pk']
                ],
            ];
        }

        return $columns;
    }

    /**
     * @inheritDoc
     */
    public function listConstraints(string $table, ?string $schema = null): array
    {
        $constraints = [];

        /** @var SQLitePlatform $platform */
        $platform = $this->getPlatform();

        $columns = $this->listColumns($table, $schema);

        $primaryKey = [];

        foreach ($columns as $name => $column) {
            if ($column['erratas']['pk']) {
                $primaryKey[] = $name;
            }
        }

        foreach ($this->loadConstraintsStatement($table, $schema) as $row) {
            if (!$row['unique']) {
                continue;
            }

            $constraint = [
                'constraint_name' => $row['name'],
                'constraint_type' => 'UNIQUE',
                'table_name'      => $table,
                'columns'         => [],
            ];

            $info = $this->db->prepare(
                $platform->pragma('index_info', $row['name'], $schema)
            );

            foreach ($info as $column) {
                $constraint['columns'][] = $column['name'];
            }

            if ($primaryKey === $constraint['columns']) {
                $constraint['constraint_type'] = 'PRIMARY KEY';
                $primaryKey = null;
            }

            $constraints[$constraint['constraint_name']] = $constraint;
        }

        return $constraints;
    }

    /**
     * @inheritDoc
     */
    public function listIndexes(string $table, ?string $schema = null): array
    {
        $indexes = [];

        /** @var SQLitePlatform $platform */
        $platform = $this->getPlatform();

        $columns = $this->listColumns($table, $schema);

        $primaryKey = [];

        foreach ($columns as $name => $column) {
            if ($column['erratas']['pk']) {
                $primaryKey[] = $name;
            }
        }

        foreach ($this->loadConstraintsStatement($table, $schema) as $row) {
            $index['table_schema']  = $schema;
            $index['table_name']    = $table;
            $index['is_unique']     = (bool) $row['unique'];
            $index['index_name']    = $row['name'];
            $index['index_comment'] = '';

            $index['columns'] = [];

            $info = $this->db->prepare(
                $platform->pragma('index_info', $row['name'], $schema)
            );

            foreach ($info as $column) {
                $index['columns'][$column['name']] = [
                    'column_name' => $column['name'],
                    'subpart' => null
                ];
            }

            if ($primaryKey === $index['columns']) {
                $index['is_primary'] = true;
                $primaryKey = null;
            }

            $indexes[$row['name']] = $index;
        }

        return $indexes;
    }
}
