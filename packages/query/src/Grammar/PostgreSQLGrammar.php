<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Grammar;

use Windwalker\Query\Clause\Clause;
use Windwalker\Query\Query;

use function Windwalker\raw;

/**
 * The PostgresqlGrammar class.
 */
class PostgreSQLGrammar extends Grammar
{
    /**
     * @var string
     */
    protected static $name = 'PostgreSQL';

    /**
     * @var string
     */
    protected static $nullDate = '1970-01-01 00:00:00';

    /**
     * @inheritDoc
     */
    public function compileLimit(Query $query, array $sql): array
    {
        $limit  = (int) $query->getLimit();
        $offset = (int) $query->getOffset();

        if ($limit > 0) {
            $sql['limit'] = 'LIMIT ' . $limit;
        }

        if ($offset > 0) {
            $sql['offset'] = 'OFFSET ' . $offset;
        }

        return $sql;
    }

    /**
     * @inheritDoc
     */
    public function listDatabases(): Query
    {
        return $this->createQuery()
            ->select('datname')
            ->from('pg_database')
            ->where('datistemplate', raw('false'));
    }

    /**
     * @inheritDoc
     */
    public function listTables(?string $dbname): Query
    {
        $query = $this->createQuery()
            ->select('table_name AS Name')
            ->from('information_schema.tables')
            ->where('table_type', 'BASE TABLE')
            ->whereNotIn('table_schema', ['pg_catalog', 'information_schema'])
            ->order('table_name', 'ASC');

        if ($dbname) {
            $query->where('table_catalog', $dbname);
        }

        return $query;
    }

    /**
     * @inheritDoc
     */
    public function dropTable(string $table, bool $ifExists = false, ...$options): string
    {
        $options[] = 'CASCADE';

        return parent::dropTable($table, $ifExists, ...$options);
    }
}
