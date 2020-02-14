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
use Windwalker\Database\Driver\Mysql\MysqlTransaction;
use Windwalker\Query\Escaper;
use Windwalker\Query\Query;
use Windwalker\Utilities\Str;

use function Windwalker\raw;

/**
 * The MysqlPlatform class.
 */
class MySQLPlatform extends AbstractPlatform
{
    protected $name = 'MySQL';

    /**
     * @inheritDoc
     */
    public function listDatabasesQuery(): Query
    {
        return $this->listSchemaQuery();
    }

    public function listSchemaQuery(): Query
    {
        return $this->createQuery()
            ->select('SCHEMA_NAME')
            ->from('INFORMATION_SCHEMA.SCHEMATA')
            ->where('SCHEMA_NAME', '!=', 'INFORMATION_SCHEMA');
    }

    public function listTablesQuery(?string $schema): Query
    {
        $query = $this->createQuery()
            ->select('TABLE_NAME')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_TYPE', 'BASE TABLE');

        if ($schema !== null) {
            $query->where('TABLE_SCHEMA', $schema);
        } else {
            $query->where('TABLE_SCHEMA', raw('(SELECT DATABASE())'));
        }

        return $query;
    }

    public function listViewsQuery(?string $schema): Query
    {
        $query = $this->createQuery()
            ->select('TABLE_NAME')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_TYPE', 'VIEW');

        if ($schema !== null) {
            $query->where('TABLE_SCHEMA', $schema);
        } else {
            $query->where('TABLE_SCHEMA', raw('(SELECT DATABASE())'));
        }

        return $query;
    }

    public function listColumnsQuery(string $table, ?string $schema): Query
    {
        $query = $this->createQuery()
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
                    'EXTRA',
                ]
            )
            ->from('INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_NAME', $this->db->replacePrefix($table));

        if ($schema !== null) {
            $query->where('TABLE_SCHEMA', $schema);
        } else {
            $query->where('TABLE_SCHEMA', raw('(SELECT DATABASE())'));
        }

        return $query;
    }

    public function listIndexesQuery(string $table, ?string $schema): Query
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
            ->where('TABLE_NAME', $this->db->replacePrefix($table));

        if ($schema !== null) {
            $query->where('TABLE_SCHEMA', $schema);
        } else {
            $query->where('TABLE_SCHEMA', raw('SELECT DATABASE()'));
        }

        return $query;
    }

    public function listConstraintsQuery(string $table, ?string $schema): Query
    {
        return $this->db->getQuery(true)
            ->select(
                [
                    'TABLE_NAME',
                    'CONSTRAINT_NAME',
                    'CONSTRAINT_TYPE',
                ]
            )
            ->from('INFORMATION_SCHEMA.TABLE_CONSTRAINTS')
            ->where('TABLE_NAME', $this->db->replacePrefix($table))
            ->tap(
                static function (Query $query) use ($schema) {
                    if ($schema !== null) {
                        $query->where('TABLE_SCHEMA', $schema);
                    } else {
                        $query->where('TABLE_SCHEMA', raw('SELECT DATABASE()'));
                    }
                }
            );
    }

    /**
     * start
     *
     * @return  static
     */
    public function transactionStart()
    {
        if (!$this->depth) {
            parent::transactionStart();
        } else {
            $savepoint = 'SP_' . $this->depth;
            $this->db->execute('SAVEPOINT ' . $this->db->quoteName($savepoint));

            $this->depth++;
        }

        return $this;
    }

    /**
     * commit
     *
     * @return  static
     */
    public function transactionCommit()
    {
        if ($this->depth <= 1) {
            parent::transactionCommit();
        } else {
            $this->depth--;
        }

        return $this;
    }

    /**
     * rollback
     *
     * @return  static
     */
    public function transactionRollback()
    {
        if ($this->depth <= 1) {
            parent::transactionRollback();
        } else {
            $savepoint = 'SP_' . ($this->depth - 1);
            $this->db->execute('ROLLBACK TO SAVEPOINT ' . $this->db->quoteName($savepoint));

            $this->depth--;
        }

        return $this;
    }
}
