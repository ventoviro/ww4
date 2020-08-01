<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform;

use Windwalker\Database\Driver\StatementInterface;
use Windwalker\Database\Schema\Schema;
use Windwalker\Query\Query;

/**
 * The SqlitePlatform class.
 */
class SQLitePlatform extends AbstractPlatform
{
    protected string $name = self::SQLITE;

    protected static ?string $defaultSchema = 'main';

    public function listDatabasesQuery(): Query
    {
        return $this->pragma('database_list');
    }

    public function listSchemaQuery(): Query
    {
        return $this->listDatabasesQuery();
    }

    public function listTablesQuery(?string $schema): Query
    {
        return $this->createQuery()
            ->select(
                [
                    'name',
                    'type',
                    'sql'
                ]
            )
            ->from(trim($schema . '.sqlite_master', '.'))
            ->where('type', 'table')
            ->where('name', 'not like', 'sqlite_%')
            ->order('name');
    }

    public function listViewsQuery(?string $schema): Query
    {
        return $this->createQuery()
            ->select(
                [
                    'name',
                    'type',
                    'sql'
                ]
            )
            ->from(trim($schema . '.sqlite_master', '.'))
            ->where('type', 'view')
            ->where('name', 'not like', 'sqlite_%')
            ->order('name');
    }

    public function listColumnsQuery(string $table, ?string $schema): Query
    {
        return $this->pragma('table_info', $this->db->replacePrefix($table), $schema);
    }

    public function listConstraintsQuery(string $table, ?string $schema): Query
    {
        return $this->listIndexesQuery($table, $schema);
    }

    public function listIndexesQuery(string $table, ?string $schema): Query
    {
        return $this->pragma('index_list', $this->db->replacePrefix($table), $schema);
    }

    public function pragma(string $name, ?string $value = null, ?string $schema = null): Query
    {
        $query = $this->createQuery();

        $sql = 'PRAGMA ';

        if (null !== $schema) {
            $sql .= $query->quoteName((string) $schema) . '.';
        }

        $sql .= $name;

        if (null !== $value) {
            $sql .= '(' . $query->quote($value) . ')';
        }

        return $query->sql($sql);
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

    /**
     * listColumns
     *
     * @param  string       $table
     * @param  string|null  $schema
     *
     * @return  array
     */
    public function listColumns(string $table, ?string $schema = null): array
    {
    }

    /**
     * listConstraints
     *
     * @param  string       $table
     * @param  string|null  $schema
     *
     * @return  array
     */
    public function listConstraints(string $table, ?string $schema = null): array
    {
    }

    /**
     * listIndexes
     *
     * @param  string       $table
     * @param  string|null  $schema
     *
     * @return  array
     */
    public function listIndexes(string $table, ?string $schema = null): array
    {
    }

    /**
     * getCurrentDatabase
     *
     * @return  string|null
     */
    public function getCurrentDatabase(): ?string
    {
    }

    /**
     * dropDatabase
     *
     * @param  string  $name
     *
     * @return  StatementInterface
     */
    public function dropDatabase(string $name): StatementInterface
    {
    }

    /**
     * createSchema
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return  StatementInterface
     */
    public function createSchema(string $name, array $options = []): StatementInterface
    {
    }

    /**
     * dropSchema
     *
     * @param  string  $name
     *
     * @return  StatementInterface
     */
    public function dropSchema(string $name): StatementInterface
    {
    }

    /**
     * createTable
     *
     * @param  Schema  $schema
     * @param  bool    $ifNotExists
     * @param  array   $options
     *
     * @return  StatementInterface
     */
    public function createTable(Schema $schema, bool $ifNotExists = false, array $options = []): StatementInterface
    {
    }

    /**
     * renameColumn
     *
     * @param  string       $table
     * @param  string       $from
     * @param  string       $to
     * @param  string|null  $schema
     *
     * @return  StatementInterface
     */
    public function renameColumn(string $table, string $from, string $to, ?string $schema = null): StatementInterface
    {
    }
}
