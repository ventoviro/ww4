<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema;

/**
 * The SQLServerSchemaManager class.
 */
class SQLServerSchemaManager extends AbstractSchemaManager
{
    /**
     * @inheritDoc
     */
    public function listColumns(string $table, ?string $schema = null): array
    {
        $columns = [];

        foreach ($this->loadColumnsStatement($table, $schema) as $row) {
            $default = preg_replace(
                "/(^(\(\(|\('|\(N'|\()|(('\)|(?<!\()\)\)|\))$))/i",
                '',
                $row['COLUMN_DEFAULT']
            );

            $columns[$row['COLUMN_NAME']] = [
                'ordinal_position' => $row['ORDINAL_POSITION'],
                'column_default' => $default,
                'is_nullable' => ('YES' === $row['IS_NULLABLE']),
                'data_type' => $row['DATA_TYPE'],
                'character_maximum_length' => $row['CHARACTER_MAXIMUM_LENGTH'],
                'character_octet_length' => $row['CHARACTER_OCTET_LENGTH'],
                'numeric_precision' => $row['NUMERIC_PRECISION'],
                'numeric_scale' => $row['NUMERIC_SCALE'],
                'numeric_unsigned' => false,
                'comment' => '',
                'auto_increment' => (bool) $row['is_identity'],
                'erratas' => [],
            ];
        }

        return $columns;
    }

    /**
     * @inheritDoc
     */
    public function listConstraints(string $table, ?string $schema = null): array
    {
        $constraintGroup = $this->loadConstraintsStatement($table, $schema)
            ->loadAll()
            ->mapProxy()
            ->apply(static function (array $storage) {
                return array_change_key_case($storage, CASE_LOWER);
            })
            ->group('constraint_name');

        $constraints = [];

        foreach ($constraintGroup as $name => $rows) {
            if ($rows[0]['constraint_type'] === 'PRIMARY KEY') {
                $name = 'PK__' . $rows[0]['table_name'];
            }

            $constraints[$name] = [
                'constraint_name' => $name,
                'constraint_type' => $rows[0]['constraint_type'],
                'table_name' => $rows[0]['table_name'],
                'columns' => [],
            ];

            if ('CHECK' === $rows[0]['constraint_type']) {
                $constraints[$name]['check_clause'] = $rows[0]['check_clause'];
                continue;
            }

            $isFK = 'FOREIGN KEY' === $rows[0]['constraint_type'];

            if ($isFK) {
                $constraints[$name]['referenced_table_schema'] = $rows[0]['referenced_table_schema'];
                $constraints[$name]['referenced_table_name']   = $rows[0]['referenced_table_name'];
                $constraints[$name]['referenced_columns']      = [];
                $constraints[$name]['match_option']            = $rows[0]['match_option'];
                $constraints[$name]['update_rule']             = $rows[0]['update_rule'];
                $constraints[$name]['delete_rule']             = $rows[0]['delete_rule'];
            }

            foreach ($rows as $row) {
                if ('CHECK' === $row['constraint_type']) {
                    $constraints[$name]['check_clause'] = $row['check_clause'];
                    continue;
                }

                $constraints[$name]['columns'][] = $row['column_name'];

                if ($isFK) {
                    $constraints[$name]['referenced_columns'][] = $row['referenced_column_name'];
                }
            }
        }

        return $constraints;
    }

    /**
     * @inheritDoc
     */
    public function listIndexes(string $table, ?string $schema = null): array
    {
        $indexGroup = $this->loadIndexesStatement($table, $schema)
            ->loadAll()
            ->group('index_name');

        $indexes = [];

        foreach ($indexGroup as $keys) {
            $index = [];
            $name  = $keys[0]['index_name'];

            if ($keys[0]['is_primary_key']) {
                $name = 'PK__' . $keys[0]['table_name'];
            }

            if ($schema === null) {
                $name = $keys[0]['table_name'] . '_' . $name;
            }

            $index['table_schema']  = $keys[0]['schema_name'];
            $index['table_name']    = $keys[0]['table_name'];
            $index['is_unique']     = (bool) $keys[0]['is_unique'];
            $index['is_primary']    = (bool) ($keys[0]['is_primary_key'] ?: $keys[0]['is_identity']);
            $index['index_name']    = $keys[0]['index_name'];
            $index['index_comment'] = '';

            $index['columns'] = [];

            foreach ($keys as $key) {
                $index['columns'][$key['column_name']] = [
                    'column_name' => $key['column_name'],
                    'sub_part' => null,
                ];
            }

            $indexes[$name] = $index;
        }

        return $indexes;
    }
}
