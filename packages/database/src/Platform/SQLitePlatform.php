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
    }

    public function listSchemaQuery(): Query
    {
    }

    public function listTablesQuery(?string $schema): Query
    {
    }

    public function listViewsQuery(?string $schema): Query
    {
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
}
