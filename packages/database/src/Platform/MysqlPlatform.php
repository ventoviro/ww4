<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform;

use Windwalker\Data\Collection;
use Windwalker\Query\Escaper;
use Windwalker\Query\Query;
use Windwalker\Utilities\Str;

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
        return $this->db->prepare(
            $this->getGrammar()->listDatabases()
        )
            ->loadColumn()
            ->dump();
    }

    /**
     * @inheritDoc
     */
    public function getTables(?string $schema = null, bool $includeViews = false): array
    {
        $tables = $this->db->prepare(
            $this->getGrammar()->listTables($schema)
        )
            ->loadColumn()
            ->dump();

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
        $query = $this->db->getQuery(true)
            ->select('TABLE_NAME')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_TYPE', 'VIEW');

        if ($schema !== null) {
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

        if ($schema !== null) {
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

            // After MariaDB 10.2.7, the COLUMN_DEFAULT will surround with quotes if is string type.
            // @see https://mariadb.com/kb/en/information-schema-columns-table/
            if (
                is_string($row['COLUMN_DEFAULT'])
                && Str::startsWith($row['COLUMN_DEFAULT'], "'")
                && Str::endsWith($row['COLUMN_DEFAULT'], "'")
            ) {
                $row['COLUMN_DEFAULT'] = Escaper::stripQuote($row['COLUMN_DEFAULT']);
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
        // JOIN of INFORMATION_SCHEMA table is very slow, we use 3 separate query to get constraints.
        // @see Commit: 4d6e7848268bd9a6add3f7ddc68e879f2f105da5
        // TODO: Test join version at MariaDB or 64bit MySQL

        // Query 1: TABLE_CONSTRAINTS
        $query = $this->db->getQuery(true)
            ->select(
                [
                    'TABLE_NAME',
                    'CONSTRAINT_NAME',
                    'CONSTRAINT_TYPE',
                ]
            )
            ->from('INFORMATION_SCHEMA.TABLE_CONSTRAINTS')
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

        $constraintItems = $this->db->prepare($query)->loadAll()->keyBy('CONSTRAINT_NAME');

        // Query 2: KEY_COLUMN_USAGE
        $query = $this->db->getQuery(true)
            ->select(
                [
                    'CONSTRAINT_NAME',
                    'COLUMN_NAME',
                    'REFERENCED_TABLE_SCHEMA',
                    'REFERENCED_TABLE_NAME',
                    'REFERENCED_COLUMN_NAME',
                ]
            )
            ->from('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')
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

        $kcuGroup = $this->db->prepare($query)->loadAll()->group('CONSTRAINT_NAME');

        // Query 3: REFERENTIAL_CONSTRAINTS
        $query = $this->db->getQuery(true)
            ->select(
                [
                    'CONSTRAINT_NAME',
                    'MATCH_OPTION',
                    'UPDATE_RULE',
                    'DELETE_RULE',
                ]
            )
            ->from('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS')
            ->where('TABLE_NAME', $table)
            ->tap(
                static function (Query $query) use ($schema) {
                    if ($schema !== null) {
                        $query->where('CONSTRAINT_SCHEMA', $schema);
                    } else {
                        $query->where('CONSTRAINT_SCHEMA', '!=', 'INFORMATION_SCHEMA');
                    }
                }
            );

        $rcItems = $this->db->prepare($query)->loadAll()->keyBy('CONSTRAINT_NAME');

        $realName    = null;
        $constraints = [];

        foreach ($constraintItems as $name => $row) {
            $kcuItems = $kcuGroup[$name] ?? new Collection();
            $rcItem   = $rcItems[$name] ?? new Collection();

            $realName = $row['CONSTRAINT_NAME'];
            $isFK     = ('FOREIGN KEY' === $row['CONSTRAINT_TYPE']);

            if ($isFK || $schema !== null) {
                $name = $realName;
            } else {
                $name = $row['TABLE_NAME'] . '_' . $realName;
            }

            $constraints[$name] = [
                'constraint_name' => $name,
                'constraint_type' => $row['CONSTRAINT_TYPE'],
                'table_name' => $row['TABLE_NAME'],
                'columns' => [],
            ];

            if ($isFK) {
                $constraints[$name]['referenced_table_schema'] = $kcuItems[0]['REFERENCED_TABLE_SCHEMA'];
                $constraints[$name]['referenced_table_name']   = $kcuItems[0]['REFERENCED_TABLE_NAME'];
                $constraints[$name]['referenced_columns']      = [];
                $constraints[$name]['match_option']            = $rcItem['MATCH_OPTION'];
                $constraints[$name]['update_rule']             = $rcItem['UPDATE_RULE'];
                $constraints[$name]['delete_rule']             = $rcItem['DELETE_RULE'];
            }

            foreach ($kcuItems as $kcuItem) {
                $constraints[$name]['columns'][] = $kcuItem['COLUMN_NAME'];

                if ($isFK) {
                    $constraints[$name]['referenced_columns'][] = $kcuItem['REFERENCED_COLUMN_NAME'];
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
        $query = $this->db->getQuery(true)
            ->select(
                [
                    'TABLE_SCHEMA',
                    'TABLE_NAME',
                    'NON_UNIQUE',
                    'INDEX_NAME',
                    'COLUMN_NAME',
                    'COLLATION',
                    'CARDINALITY',
                    'SUB_PART',
                    'INDEX_COMMENT',
                ]
            )
            ->from('INFORMATION_SCHEMA.STATISTICS')
            ->where('TABLE_NAME', $table);

        if ($schema !== null) {
            $query->where('TABLE_SCHEMA', $schema);
        } else {
            $query->where('TABLE_SCHEMA', '!=', 'INFORMATION_SCHEMA');
        }

        $indexGroup = $this->db->prepare($query)->loadAll()->group('INDEX_NAME');

        $indexes = [];

        foreach ($indexGroup as $keys) {
            $index = [];
            $name  = $keys[0]['INDEX_NAME'];

            if ($schema === null) {
                $name = $keys[0]['TABLE_NAME'] . '_' . $name;
            }

            $index['table_schema']  = $keys[0]['TABLE_SCHEMA'];
            $index['table_name']    = $keys[0]['TABLE_NAME'];
            $index['is_unique']     = (string) $keys[0]['NON_UNIQUE'] === '0';
            $index['is_primary']    = $keys[0]['INDEX_NAME'] === 'PRIMARY';
            $index['index_name']    = $keys[0]['INDEX_NAME'];
            $index['index_comment'] = $keys[0]['INDEX_COMMENT'];

            $index['columns'] = [];

            foreach ($keys as $key) {
                $index['columns'][$key['COLUMN_NAME']] = [
                    'column_name' => $key['COLUMN_NAME'],
                    'sub_part' => $key['SUB_PART'],
                ];
            }

            $indexes[$name] = $index;
        }

        return $indexes;
    }
}
