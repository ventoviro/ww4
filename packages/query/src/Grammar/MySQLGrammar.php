<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Grammar;

use Windwalker\Query\Query;

use function Windwalker\raw;

/**
 * The MySQLGrammar class.
 */
class MySQLGrammar extends AbstractGrammar
{
    /**
     * @var string
     */
    protected static $name = 'MySQL';

    /**
     * @var array
     */
    protected static $nameQuote = ['`', '`'];

    /**
     * @var string
     */
    protected static $nullDate = '1000-01-01 00:00:00';

    /**
     * If no connection set, we escape it with default function.
     *
     * Since mysql_real_escape_string() has been deprecated, we use an alternative one.
     * Please see:
     * http://stackoverflow.com/questions/4892882/mysql-real-escape-string-for-multibyte-without-a-connection
     *
     * @param string $text
     *
     * @return  string
     */
    public function localEscape(string $text): string
    {
        return str_replace(
            ['\\', "\0", "\n", "\r", "'", '"', "\x1a"],
            ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'],
            $text
        );
    }

    /**
     * @inheritDoc
     */
    public function listDatabases($where = null): Query
    {
        return $this->createQuery()
            ->select('SCHEMA_NAME')
            ->from('INFORMATION_SCHEMA.SCHEMATA')
            ->where('SCHEMA_NAME', '!=', 'INFORMATION_SCHEMA');
    }

    /**
     * @inheritDoc
     */
    public function listTables(?string $schema = null): Query
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

    /**
     * @inheritDoc
     */
    public function listViews(?string $schema = null): Query
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
}
