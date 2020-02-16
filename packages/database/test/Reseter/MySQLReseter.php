<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Reseter;

use Windwalker\Database\Test\AbstractDatabaseTestCase;
use Windwalker\Query\Grammar\AbstractGrammar;

/**
 * The AbstractMySQLTestCase class.
 */
class MySQLReseter extends AbstractReseter
{
    protected static $platform = 'mysql';

    public function createDatabase(\PDO $pdo, string $dbname): void
    {
        $pdo->exec('DROP DATABASE IF EXISTS ' . static::qn($dbname));

        // DatabaseManager created
        $pdo->exec('DROP DATABASE IF EXISTS ' . static::qn($dbname . '_new'));

        $pdo->exec('CREATE DATABASE ' . static::qn($dbname));
    }

    public function clearAllTables(\PDO $pdo, string $dbname): void
    {
        //
    }
}
