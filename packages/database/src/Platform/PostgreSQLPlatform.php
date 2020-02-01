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

    public function listDatabasesQuery(): Query
    {
        return $this->createQuery()
            ->select('datname')
            ->from('pg_database')
            ->where('datistemplate', raw('false'));
    }

    public function listSchemaQuery(): Query
    {
        return $this->db->getQuery(true)
            ->select('schema_name')
            ->from('information_schema.schemata');
    }

    public function listTablesQuery(?string $schema): Query
    {
        $query = $this->createQuery()
            ->select('table_name AS Name')
            ->from('information_schema.tables')
            ->where('table_type', 'BASE TABLE')
            ->order('table_name', 'ASC');

        if ($schema) {
            $query->where('table_schema', $schema);
        } else {
            $query->whereNotIn('table_schema', ['pg_catalog', 'information_schema']);
        }

        return $query;
    }

    public function listViewsQuery(?string $schema): Query
    {
        $query = $this->createQuery()
            ->select('table_name AS Name')
            ->from('information_schema.tables')
            ->where('table_type', 'VIEW')
            ->order('table_name', 'ASC');

        if ($schema) {
            $query->where('table_schema', $schema);
        } else {
            $query->whereNotIn('table_schema', ['pg_catalog', 'information_schema']);
        }

        return $query;
    }

    public function listColumnsQuery(string $table, ?string $schema): Query
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

        return $query;
    }

    public function listConstraintsQuery(string $table, ?string $schema): Query
    {
        $query = $this->createQuery()->select(
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

        return $query;
    }

    public function listIndexesQuery(string $table, ?string $schema): Query
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

        return $query;
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
