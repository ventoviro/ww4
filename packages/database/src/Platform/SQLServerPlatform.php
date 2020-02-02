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
