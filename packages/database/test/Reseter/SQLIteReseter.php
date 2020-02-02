<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Reseter;

/**
 * The SQLIteReseter class.
 */
class SQLIteReseter extends AbstractReseter
{
    protected static $platform = 'SQLite';

    public function createDatabase(\PDO $pdo, string $dbname): void
    {
        if ($dbname !== ':memory:' && is_file($dbname)) {
            @unlink($dbname);
        }
    }

    public function clearAllTables(\PDO $pdo, string $dbname): void
    {
        //
    }
}
