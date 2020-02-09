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

/**
 * The SqlitePlatform class.
 */
class SQLitePlatform extends AbstractPlatform
{
    protected $name = 'SQLite';

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
    }

    public function listConstraintsQuery(string $table, ?string $schema): Query
    {
    }

    public function listIndexesQuery(string $table, ?string $schema): Query
    {
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
            $sql .= '(\'' . $query->quote($value) . '\')';
        }

        return $query->sql($sql);
    }
}
