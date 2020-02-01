<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform;

use Windwalker\Database\Driver\Pdo\PdoDriver;
use Windwalker\Query\Clause\JoinClause;
use Windwalker\Query\Escaper;
use Windwalker\Query\Query;

use Windwalker\Scalars\ArrayObject;
use Windwalker\Utilities\Str;

use function Windwalker\raw;

/**
 * The PostgresqlPlatform class.
 */
class PostgreSQLPlatform extends AbstractPlatform
{
    protected $name = 'PostgreSQL';

    /**
     * @inheritDoc
     */
    public function getSchemas(): array
    {
        $query = $this->db->getQuery(true)
            ->select('schema_name')
            ->from('information_schema.schemata');

        return $this->db->prepare($query)->loadColumn()->dump();
    }

    /**
     * @inheritDoc
     */
    public function getColumns(string $table, ?string $schema = null): array
    {
        $query = $this->db->getQuery(true)
            ->select(
                [
                    'ordinal_position',
                    'column_default',
                    'is_nullable',
                    'data_type',
                    'character_maximum_length',
                    'character_octet_length',
                    'numeric_precision',
                    'numeric_scale',
                    'column_name'
                ]
            )
            ->from('information_schema.columns')
            ->where('table_name', $table);

        if ($schema !== null) {
            $query->where('table_schema', $schema);
        } else {
            $query->whereNotIn('table_schema', ['pg_catalog', 'information_schema']);
        }

        $columns = [];

        foreach ($this->db->prepare($query) as $row) {
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
    public function getConstraints(string $table, ?string $schema = null): array
    {
        $query = $this->createQuery();

        $query->select(
            [
                't.table_name',
                'tc.constraint_name',
                'tc.constraint_type',
                'kcu.column_name',
                'cc.check_clause',
                'rc.match_option',
                'rc.update_rule',
                'rc.delete_rule',
                'kcu2.table_schema AS referenced_table_schema',
                'kcu2.table_name AS referenced_table_name',
                'kcu2.column_name AS referenced_column_name',
            ]
        )
            ->from('information_schema.tables', 't')
            ->innerJoin(
                'information_schema.table_constraints',
                'tc',
                [
                    ['t.table_schema', '=', 'tc.table_schema'],
                    ['t.table_name', '=', 'tc.table_name']
                ]
            )
            ->leftJoin(
                'information_schema.key_column_usage',
                'kcu',
                [
                    ['kcu.table_schema', '=', 'tc.table_schema'],
                    ['kcu.table_name', '=', 'tc.table_name'],
                    ['kcu.constraint_name', '=', 'tc.constraint_name'],
                ]
            )
            ->leftJoin(
                'information_schema.check_constraints',
                'cc',
                [
                    ['cc.constraint_schema', '=', 'tc.constraint_schema'],
                    ['cc.constraint_name', '=', 'tc.constraint_name'],
                ]
            )
            ->leftJoin(
                'information_schema.referential_constraints',
                'rc',
                [
                    ['rc.constraint_schema', '=', 'tc.constraint_schema'],
                    ['rc.constraint_name', '=', 'tc.constraint_name'],
                ]
            )
            ->leftJoin(
                'information_schema.key_column_usage',
                'kcu2',
                [
                    ['rc.unique_constraint_schema', '=', 'kcu2.constraint_schema'],
                    ['rc.unique_constraint_name', '=', 'kcu2.constraint_name'],
                    ['kcu.position_in_unique_constraint', '=', 'kcu2.ordinal_position'],
                ]
            )
            ->where('t.table_name', $table)
            ->where('t.table_type', 'in', ['BASE TABLE', 'VIEW']);

        if ($schema !== null) {
            $query->where('t.table_schema', $schema);
        } else {
            $query->whereNotIn('table_schema', ['pg_catalog', 'information_schema']);
        }

        $order = 'CASE tc.constraint_type'
            . " WHEN 'PRIMARY KEY' THEN 1"
            . " WHEN 'UNIQUE' THEN 2"
            . " WHEN 'FOREIGN KEY' THEN 3"
            . " WHEN 'CHECK' THEN 4"
            . ' ELSE 5 END'
            . ', tc.constraint_name'
            . ', kcu.ordinal_position';

        $query->order(raw($order));

        $constraintGroup = $this->db->prepare($query)->loadAll()->group('constraint_name');

        $name = null;
        $constraints = [];

        foreach ($constraintGroup as $name => $rows) {
            $constraints[$name] = [
                'constraint_name' => $name,
                'constraint_type' => $rows[0]['constraint_type'],
                'table_name'      => $rows[0]['table_name'],
                'columns' => []
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
                $constraints[$name]['match_option']       = $rows[0]['match_option'];
                $constraints[$name]['update_rule']        = $rows[0]['update_rule'];
                $constraints[$name]['delete_rule']        = $rows[0]['delete_rule'];
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
    public function getIndexes(string $table, ?string $schema = null): array
    {
        $query = $this->createQuery();

        $query->select('ix.*')
            ->selectAs(raw('tc.constraint_type = \'PRIMARY KEY\''), 'is_primary')
            ->from('pg_indexes', 'ix')
            ->leftJoin(
                'information_schema.table_constraints',
                'tc',
                static function (JoinClause $join) {
                    $join->on('tc.table_schema', 'ix.schemaname');
                    $join->on('tc.constraint_name', 'ix.indexname');
                    $join->onRaw('tc.constraint_type = %q', 'PRIMARY KEY');
                }
            )
            ->where('tablename', $table);

        $order = 'CASE tc.constraint_type WHEN \'PRIMARY KEY\' THEN 1 ELSE 2 END';

        $query->order(raw($order));

        if ($schema !== null) {
            $query->where('schemaname', $schema);
        } else {
            $query->whereNotIn('schemaname', ['pg_catalog', 'information_schema']);
        }

        $indexes = [];

        foreach ($this->db->prepare($query) as $row) {
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
                ->map(static function (string $index) {
                    return Escaper::stripQuoteIfExists($index, '"');
                })
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

    public function lastInsertId($insertQuery, ?string $sequence = null): ?string
    {
        if ($sequence && $this->db->getDriver() instanceof PdoDriver) {
            /** @var \PDO $pdo */
            $pdo = $this->db->getDriver()->getConnection()->get();
            return $pdo->lastInsertId($sequence);
        }

        if ($insertQuery instanceof Query) {
            $table = $insertQuery->getInsert()->getElements();
        } else {
            preg_match('/insert\s*into\s*[\"]*(\W\w+)[\"]*/i', $insertQuery, $matches);

            if (!isset($matches[1])) {
                return null;
            }

            $table = [$matches[1]];
        }

        /* find sequence column name */
        $colNameQuery = $this->createQuery();

        $colNameQuery->select('column_default')
            ->from('information_schema.columns')
            ->where('table_name', $this->db->replacePrefix(trim($table[0], '" ')))
            ->where('column_default', 'LIKE', '%nextval%');

        $colName = $this->db->prepare($colNameQuery)->loadOne()->first();

        $changedColName = str_replace('nextval', 'currval', $colName);

        $insertidQuery = $this->createQuery();

        $insertidQuery->selectRaw($changedColName);

        try {
            return $this->db->prepare($insertidQuery)->loadResult();
        } catch (\PDOException $e) {
            // 55000 means we trying to insert value to serial column
            // Just return because insertedId get the last generated value.
            if ($e->getCode() !== 55000) {
                throw $e;
            }
        }

        return null;
    }
}
