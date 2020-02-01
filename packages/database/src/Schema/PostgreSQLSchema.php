<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema;

use Windwalker\Query\Escaper;
use Windwalker\Scalars\ArrayObject;

use function Windwalker\raw;

/**
 * The PostgreSQLSchema class.
 */
class PostgreSQLSchema extends AbstractSchema
{
    /**
     * @inheritDoc
     */
    public function listColumns(string $table, ?string $schema = null): array
    {
        $columns = [];

        foreach ($this->getPlatform()->listColumnsQuery($table, $schema) as $row) {
            $columns[$row['column_name']] = [
                'ordinal_position' => $row['ordinal_position'],
                'column_default' => $row['column_default'],
                'is_nullable' => ('YES' === $row['is_nullable']),
                'data_type' => $row['data_type'],
                'character_maximum_length' => $row['character_maximum_length'],
                'character_octet_length' => $row['character_octet_length'],
                'numeric_precision' => $row['numeric_precision'],
                'numeric_scale' => $row['numeric_scale'],
                'numeric_unsigned' => false,
                'auto_increment' => false,
                'comment' => '',
                'erratas' => [],
            ];
        }

        foreach ($columns as &$column) {
            if (strpos((string) $column['column_default'], 'nextval') !== false) {
                $column['auto_increment'] = true;
                $column['column_default'] = 0;
            }

            if (preg_match('/^NULL::*/', (string) $column['column_default'])) {
                $column['column_default'] = null;
            }

            if (preg_match("/'(.*)'::[\w\s]/", (string) $column['column_default'], $matches)) {
                $column['column_default'] = $matches[1] ?? '';
            }

            if (strpos((string) $column['data_type'], 'character varying') !== false) {
                $column['data_type'] = str_replace('character varying', 'varchar', $column['data_type']);
            }
        }

        return $columns;
    }

    /**
     * @inheritDoc
     */
    public function listConstraints(string $table, ?string $schema = null): array
    {
        $constraintGroup = $this->db->prepare(
            $this->getPlatform()
                ->listConstraintsQuery($table, $schema)
        )
            ->loadAll()
            ->group('constraint_name');

        $name        = null;
        $constraints = [];

        foreach ($constraintGroup as $name => $rows) {
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
        $indexes = [];

        foreach ($this->db->prepare($this->getPlatform()->listIndexesQuery($table, $schema)) as $row) {
            preg_match(
                '/CREATE (UNIQUE )?INDEX [\w]+ ON [\w.]+ USING [\w]+ \(([\w, ]+)\)/',
                $row['indexdef'],
                $matches
            );

            $index['table_schema']  = $row['schemaname'];
            $index['table_name']    = $row['tablename'];
            $index['is_unique']     = trim($matches[1]) === 'UNIQUE';
            $index['is_primary']    = (bool) $row['is_primary'];
            $index['index_name']    = $row['indexname'];
            $index['index_comment'] = '';

            $index['columns'] = [];

            $columns = ArrayObject::explode(',', $matches[2])
                ->map('trim')
                ->map(
                    static function (string $index) {
                        return Escaper::stripQuoteIfExists($index, '"');
                    }
                )
                ->dump();

            foreach ($columns as $column) {
                $index['columns'][$column] = [
                    'column_name' => $column,
                    'sub_part' => null,
                ];
            }

            $indexes[$row['indexname']] = $index;
        }

        return $indexes;
    }
}
