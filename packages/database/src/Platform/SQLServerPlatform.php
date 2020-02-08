<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform;

use Windwalker\Query\Query;

use function Windwalker\Query\raw_format;
use function Windwalker\raw;

/**
 * The SqlserverPlatform class.
 */
class SQLServerPlatform extends AbstractPlatform
{
    protected $name = 'SQLServer';

    public function listDatabasesQuery(): Query
    {
        return $this->db->getQuery(true)
            ->select('name')
            ->from('master.dbo.sysdatabases');
    }

    public function listSchemaQuery(): Query
    {
        return $this->db->getQuery(true)
            ->select('SCHEMA_NAME')
            ->from('INFORMATION_SCHEMA.SCHEMATA')
            ->where('SCHEMA_NAME', '!=', 'INFORMATION_SCHEMA');
    }

    public function listTablesQuery(?string $schema): Query
    {
        return $this->createQuery()
            ->select('TABLE_NAME')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_TYPE', 'BASE TABLE')
            ->tap(
                static function (Query $query) use ($schema) {
                    if ($schema !== null) {
                        $query->where('TABLE_SCHEMA', $schema);
                    } else {
                        $query->where('TABLE_SCHEMA', '!=', 'INFORMATION_SCHEMA');
                    }
                }
            )
            ->order('TABLE_NAME');
    }

    public function listViewsQuery(?string $schema): Query
    {
        return $this->createQuery()
            ->select('TABLE_NAME')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_TYPE', 'VIEW')
            ->tap(
                static function (Query $query) use ($schema) {
                    if ($schema !== null) {
                        $query->where('TABLE_SCHEMA', $schema);
                    } else {
                        $query->where('TABLE_SCHEMA', '!=', 'INFORMATION_SCHEMA');
                    }
                }
            )
            ->order('TABLE_NAME');
    }

    public function listColumnsQuery(string $table, ?string $schema): Query
    {
        return $this->createQuery()
            ->select(
                [
                    'c.ORDINAL_POSITION',
                    'c.COLUMN_DEFAULT',
                    'c.IS_NULLABLE',
                    'c.DATA_TYPE',
                    'c.CHARACTER_MAXIMUM_LENGTH',
                    'c.CHARACTER_OCTET_LENGTH',
                    'c.NUMERIC_PRECISION',
                    'c.NUMERIC_SCALE',
                    'c.COLUMN_NAME',
                    'sc.is_identity',
                ]
            )
            ->from('INFORMATION_SCHEMA.COLUMNS', 'c')
            ->leftJoin(
                'sys.columns',
                'sc',
                [
                    ['sc.object_id', '=', raw('object_id(c.TABLE_NAME)')],
                    ['sc.name', '=', 'c.COLUMN_NAME'],
                ]
            )
            ->where('TABLE_NAME', $table)
            ->tap(
                static function (Query $query) use ($schema) {
                    if ($schema !== null) {
                        $query->where('TABLE_SCHEMA', $schema);
                    } else {
                        $query->where('TABLE_SCHEMA', '!=', 'INFORMATION_SCHEMA');
                    }
                }
            );
    }

    public function listConstraintsQuery(string $table, ?string $schema): Query
    {
        return $this->createQuery()
            ->select(
                [
                    'T.TABLE_NAME',
                    'TC.CONSTRAINT_NAME',
                    'TC.CONSTRAINT_TYPE',
                    'KCU.COLUMN_NAME',
                    'CC.CHECK_CLAUSE',
                    'RC.MATCH_OPTION',
                    'RC.UPDATE_RULE',
                    'RC.DELETE_RULE',
                    'KCU2.TABLE_SCHEMA AS REFERENCED_TABLE_SCHEMA',
                    'KCU2.TABLE_NAME AS REFERENCED_TABLE_NAME',
                    'KCU2.COLUMN_NAME AS REFERENCED_COLUMN_NAME',
                ]
            )
            ->from('INFORMATION_SCHEMA.TABLES', 'T')
            ->innerJoin(
                'INFORMATION_SCHEMA.TABLE_CONSTRAINTS',
                'TC',
                [
                    ['T.TABLE_SCHEMA', '=', 'TC.TABLE_SCHEMA'],
                    ['T.TABLE_NAME', '=', 'TC.TABLE_NAME'],
                ]
            )
            ->leftJoin(
                'INFORMATION_SCHEMA.KEY_COLUMN_USAGE',
                'KCU',
                [
                    ['KCU.TABLE_SCHEMA', '=', 'TC.TABLE_SCHEMA'],
                    ['KCU.TABLE_NAME', '=', 'TC.TABLE_NAME'],
                    ['KCU.CONSTRAINT_NAME', '=', 'TC.CONSTRAINT_NAME'],
                ]
            )
            ->leftJoin(
                'INFORMATION_SCHEMA.CHECK_CONSTRAINTS',
                'CC',
                [
                    ['CC.CONSTRAINT_SCHEMA', '=', 'TC.CONSTRAINT_SCHEMA'],
                    ['CC.CONSTRAINT_NAME', '=', 'TC.CONSTRAINT_NAME'],
                ]
            )
            ->leftJoin(
                'INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS',
                'RC',
                [
                    ['RC.CONSTRAINT_SCHEMA', '=', 'TC.CONSTRAINT_SCHEMA'],
                    ['RC.CONSTRAINT_NAME', '=', 'TC.CONSTRAINT_NAME'],
                ]
            )
            ->leftJoin(
                'INFORMATION_SCHEMA.KEY_COLUMN_USAGE',
                'KCU2',
                [
                    ['RC.UNIQUE_CONSTRAINT_SCHEMA', '=', 'KCU2.CONSTRAINT_SCHEMA'],
                    ['RC.UNIQUE_CONSTRAINT_NAME', '=', 'KCU2.CONSTRAINT_NAME'],
                    ['KCU.ORDINAL_POSITION', '=', 'KCU2.ORDINAL_POSITION'],
                ]
            )
            ->where('T.TABLE_NAME', $table)
            ->where('T.TABLE_TYPE', 'IN', ['BASE table', 'VIEW'])
            ->tap(
                static function (Query $query) use ($schema) {
                    if ($schema !== null) {
                        $query->WHERE('T.TABLE_SCHEMA', $schema);
                    } else {
                        $query->WHERENOTIN('TABLE_SCHEMA', ['PG_CATALOG', 'INFORMATION_SCHEMA']);
                    }

                    $order = 'CASE%n'
                        . " WHEN 'PRIMARY KEY' THEN 1"
                        . " WHEN 'UNIQUE' THEN 2"
                        . " WHEN 'FOREIGN KEY' THEN 3"
                        . " WHEN 'CHECK' THEN 4"
                        . ' ELSE 5 END'
                        . ', %n'
                        . ', %n';

                    $query->order(
                        $query->raw(
                            $order,
                            'TC.CONSTRAINT_TYPE',
                            'TC.CONSTRAINT_NAME',
                            'KCU.ORDINAL_POSITION'
                        )
                    );
                }
            );
    }

    public function listIndexesQuery(string $table, ?string $schema): Query
    {
        return $this->createQuery()
            ->selectRaw('schema_name(tbl.schema_id) AS schema_name')
            ->select(
                [
                    'tbl.name AS table_name',
                    'col.name AS column_name',
                    'idx.name AS index_name',
                    'col.*',
                    'idx.*',
                ]
            )
            ->from('sys.columns AS col')
            ->leftJoin(
                'sys.tables',
                'tbl',
                'col.object_id',
                '=',
                'tbl.object_id'
            )
            ->leftJoin(
                'sys.index_columns',
                'ic',
                [
                    ['col.column_id', '=', 'ic.column_id'],
                    ['ic.object_id', '=', 'tbl.object_id']
                ]
            )
            ->leftJoin(
                'sys.indexes',
                'idx',
                [
                    ['idx.object_id', '=', 'tbl.object_id'],
                    ['idx.index_id', '=', 'ic.index_id']
                ]
            )
            ->where('tbl.name', $table)
            ->orWhere(function (Query $query) {
                $query->where('idx.name', '!=', null);
                $query->where('col.is_identity', 1);
                $query->where('idx.is_primary_key', 1);
            });
    }
}
