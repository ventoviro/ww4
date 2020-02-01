<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Reseter;

use function Windwalker\raw;

/**
 * The PostgreSQLTrait class.
 */
class PostgreSQLReseter extends AbstractReseter
{
    protected static $platform = 'PostgreSQL';

    public function createDatabase(\PDO $pdo, string $dbname): void
    {
        $dbs = $pdo->query(
            static::createQuery()
                ->select('datname')
                ->from('pg_database')
                ->where('datistemplate', raw('false'))
        )
            ->fetchAll(\PDO::FETCH_COLUMN) ?: [];

        if (!in_array($dbname, $dbs, true)) {
            $pdo->exec('CREATE DATABASE ' . static::qn($dbname));
        }
    }

    public function clearAllTables(\PDO $pdo, string $dbname): void
    {
        // Drop Tables
        $tables = $pdo->query(
            static::createQuery()
                ->select('table_name AS Name')
                ->from('information_schema.tables')
                ->where('table_type', 'BASE TABLE')
                ->order('table_name', 'ASC')
                ->whereNotIn('table_schema', ['pg_catalog', 'information_schema'])
        )->fetchAll(\PDO::FETCH_COLUMN) ?: [];

        if ($tables) {
            foreach ($tables as $table) {
                $pdo->exec(
                    static::createQuery()->format(
                        'DROP TABLE %n CASCADE',
                        $table
                    )
                );
            }
        }

        // Drop Views
        $tables = $pdo->query(
            static::createQuery()
                ->select('table_name AS Name')
                ->from('information_schema.tables')
                ->where('table_type', 'VIEW')
                ->order('table_name', 'ASC')
                ->whereNotIn('table_schema', ['pg_catalog', 'information_schema'])
        )->fetchAll(\PDO::FETCH_COLUMN) ?: [];

        if ($tables) {
            foreach ($tables as $table) {
                $pdo->exec(
                    static::createQuery()->format(
                        'DROP TABLE %n CASCADE',
                        $table
                    )
                );
            }
        }
    }
}
