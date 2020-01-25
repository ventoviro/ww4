<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform;

use function Windwalker\raw;

/**
 * The MysqlPlatform class.
 */
class MysqlPlatform extends AbstractPlatform
{
    protected $name = 'mysql';

    /**
     * @inheritDoc
     */
    public function getSchemas(): array
    {
        $query = $this->db->getQuery(true)
            ->select('SCHEMA_NAME')
            ->from('INFORMATION_SCHEMA.SCHEMATA')
            ->where('SCHEMA_NAME', '!=', 'INFORMATION_SCHEMA');

        return $this->schemas = $this->db->prepare($query)->loadColumn()->dump();
    }

    /**
     * @inheritDoc
     */
    public function getTables(?string $schema = null, bool $includeViews = false): array
    {
        $schema = $schema ?? static::DEFAULT_SCHEMA;

        $query = $this->db->getQuery(true)
            ->select('TABLE_NAME')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_TYPE', 'BASE TABLE');

        if ($schema !== self::DEFAULT_SCHEMA) {
            $query->where('TABLE_SCHEMA', $schema);
        } else {
            $query->where('TABLE_SCHEMA', '!=', 'INFORMATION_SCHEMA');
        }

        $tables = $this->db->prepare($query)->loadColumn()->dump();

        if ($includeViews) {
            $tables = array_merge(
                $tables,
                $this->getViews($schema)
            );
        }

        return $tables;
    }

    /**
     * @inheritDoc
     */
    public function getViews(?string $schema = null): array
    {
        $schema = $schema ?? static::DEFAULT_SCHEMA;

        $query = $this->db->getQuery(true)
            ->select('TABLE_NAME')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_TYPE', 'VIEW');

        if ($schema !== self::DEFAULT_SCHEMA) {
            $query->where('TABLE_SCHEMA', $schema);
        } else {
            $query->where('TABLE_SCHEMA', '!=', 'INFORMATION_SCHEMA');
        }

        return $this->db->prepare($query)->loadColumn()->dump();
    }

    /**
     * @inheritDoc
     */
    public function getColumns(string $table, ?string $schema = null): array
    {
        $schema = $schema ?? static::DEFAULT_SCHEMA;

        $query = $this->db->getQuery(true)
            ->select(
                [
                    'ORDINAL_POSITION',
                    'COLUMN_DEFAULT',
                    'IS_NULLABLE',
                    'DATA_TYPE',
                    'CHARACTER_MAXIMUM_LENGTH',
                    'CHARACTER_OCTET_LENGTH',
                    'NUMERIC_PRECISION',
                    'NUMERIC_SCALE',
                    'COLUMN_NAME',
                    'COLUMN_TYPE',
                    'COLUMN_COMMENT',
                ]
            )
            ->from('INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_NAME', $table);

        if ($schema !== self::DEFAULT_SCHEMA) {
            $query->where('TABLE_SCHEMA', $schema);
        } else {
            $query->where('TABLE_SCHEMA', '!=', 'INFORMATION_SCHEMA');
        }

        $columns = [];

        foreach ($this->db->prepare($query) as $row) {
            $erratas = [];
            $matches = [];

            if (preg_match('/^(?:enum|set)\((.+)\)$/i', $row['COLUMN_TYPE'], $matches)) {
                $permittedValues = $matches[1];

                if (
                preg_match_all(
                    "/\\s*'((?:[^']++|'')*+)'\\s*(?:,|\$)/",
                    $permittedValues,
                    $matches,
                    PREG_PATTERN_ORDER
                )
                ) {
                    $permittedValues = str_replace("''", "'", $matches[1]);
                } else {
                    $permittedValues = [$permittedValues];
                }

                $erratas['permitted_values'] = $permittedValues;
            }

            $columns[$row['COLUMN_NAME']] = [
                'ordinal_position' => $row['ORDINAL_POSITION'],
                'column_default' => $row['COLUMN_DEFAULT'],
                'is_nullable' => ('YES' === $row['IS_NULLABLE']),
                'data_type' => $row['DATA_TYPE'],
                'character_maximum_length' => $row['CHARACTER_MAXIMUM_LENGTH'],
                'character_octet_length' => $row['CHARACTER_OCTET_LENGTH'],
                'numeric_precision' => $row['NUMERIC_PRECISION'],
                'numeric_scale' => $row['NUMERIC_SCALE'],
                'numeric_unsigned' => (false !== strpos($row['COLUMN_TYPE'], 'unsigned')),
                'comment' => $row['COLUMN_COMMENT'],
                'erratas' => $erratas,
            ];
        }

        return $columns;
    }

    /**
     * @inheritDoc
     */
    public function getConstraints(string $table, ?string $schema = null): array
    {
        $schema = $schema ?? static::DEFAULT_SCHEMA;

        $query = $this->db->getQuery(true)
            ->select(
                [
                    'T.TABLE_NAME',
                    'TC.CONSTRAINT_NAME',
                    'TC.CONSTRAINT_TYPE',
                    'KCU.COLUMN_NAME',
                    'RC.MATCH_OPTION',
                    'RC.UPDATE_RULE',
                    'RC.DELETE_RULE',
                    'KCU.REFERENCED_TABLE_SCHEMA',
                    'KCU.REFERENCED_TABLE_NAME',
                    'KCU.REFERENCED_COLUMN_NAME',
                ]
            )
            ->from('INFORMATION_SCHEMA.TABLES', 'T')
            ->leftJoin(
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
                    ['T.TABLE_SCHEMA', '=', 'KCU.TABLE_SCHEMA'],
                    ['T.TABLE_NAME', '=', 'KCU.TABLE_NAME'],
                    ['TC.CONSTRAINT_NAME', '=', 'KCU.CONSTRAINT_NAME'],
                ]
            )
            ->leftJoin(
                'INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS',
                'RC',
                [
                    ['TC.CONSTRAINT_SCHEMA', '=', 'RC.CONSTRAINT_SCHEMA'],
                    ['TC.CONSTRAINT_NAME', '=', 'RC.CONSTRAINT_NAME'],
                ]
            )
            ->where('T.TABLE_NAME', $table)
            ->whereIn('T.TABLE_TYPE', ['BASE TABLE', 'VIEW']);

        if ($schema !== self::DEFAULT_SCHEMA) {
            $query->where('T.TABLE_SCHEMA', $schema);
        } else {
            $query->where('T.TABLE_SCHEMA', '!=', 'INFORMATION_SCHEMA');
        }

        $query->order(
            raw(
                $query->format('CASE %n', 'TC.CONSTRAINT_TYPE')
                . " WHEN 'PRIMARY KEY' THEN 1"
                . " WHEN 'UNIQUE' THEN 2"
                . " WHEN 'FOREIGN KEY' THEN 3"
                . ' ELSE 4 END'
            )
        )
            ->order('TC.CONSTRAINT_NAME')
            ->order('KCU.ORDINAL_POSITION');
        echo $query->debug();
        $realName    = null;
        $constraints = [];

        foreach ($this->db->prepare($query) as $row) {
            if ($row['CONSTRAINT_NAME'] !== $realName) {
                $realName = $row['CONSTRAINT_NAME'];
                $isFK     = ('FOREIGN KEY' === $row['CONSTRAINT_TYPE']);

                if ($isFK) {
                    $name = $realName;
                } else {
                    $name = '_ww_' . $row['TABLE_NAME'] . '_' . $realName;
                }

                $constraints[$name] = [
                    'constraint_name' => $name,
                    'constraint_type' => $row['CONSTRAINT_TYPE'],
                    'table_name' => $row['TABLE_NAME'],
                    'columns' => [],
                ];

                if ($isFK) {
                    $constraints[$name]['referenced_table_schema'] = $row['REFERENCED_TABLE_SCHEMA'];
                    $constraints[$name]['referenced_table_name']   = $row['REFERENCED_TABLE_NAME'];
                    $constraints[$name]['referenced_columns']      = [];
                    $constraints[$name]['match_option']            = $row['MATCH_OPTION'];
                    $constraints[$name]['update_rule']             = $row['UPDATE_RULE'];
                    $constraints[$name]['delete_rule']             = $row['DELETE_RULE'];
                }
            }

            $constraints[$name]['columns'][] = $row['COLUMN_NAME'];

            if ($isFK) {
                $constraints[$name]['referenced_columns'][] = $row['REFERENCED_COLUMN_NAME'];
            }
        }

        return $constraints;
    }

    /**
     * @inheritDoc
     */
    public function getConstraintKeys(string $constraint, string $table, ?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getTriggerNames(?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getTriggers(?string $schema = null): array
    {
    }
}
