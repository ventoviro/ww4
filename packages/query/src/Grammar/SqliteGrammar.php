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

/**
 * The MySQLGrammar class.
 */
class SqliteGrammar extends Grammar
{
    /**
     * @var string
     */
    protected static $name = 'Sqlite';

    /**
     * @var array
     */
    protected static $nameQuote = ['`', '`'];

    /**
     * @inheritDoc
     */
    public function listTables(?string $dbname): Query
    {
        return parent::listTables($dbname);
    }

    /**
     * @inheritDoc
     */
    public function dropTable(string $table, bool $ifExists = false, ...$options): string
    {
        return parent::dropTable($table, $ifExists, $options);
    }
}
