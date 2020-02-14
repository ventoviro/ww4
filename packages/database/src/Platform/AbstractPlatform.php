<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Driver\TransactionDriverInterface;
use Windwalker\Query\Grammar\AbstractGrammar;
use Windwalker\Query\Query;

/**
 * The AbstractPlatform class.
 */
abstract class AbstractPlatform
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var AbstractGrammar
     */
    protected $grammar;

    /**
     * @var DatabaseAdapter
     */
    protected $db;

    /**
     * The depth of the current transaction.
     *
     * @var  int
     */
    protected $depth = 0;

    public static function create(string $platform, DatabaseAdapter $db)
    {
        $class = __NAMESPACE__ . '\\' . static::getPlatformName($platform) . 'Platform';

        return new $class($db);
    }

    public static function getPlatformName(string $platform): string
    {
        switch (strtolower($platform)) {
            case 'pgsql':
            case 'postgresql':
                $platform = 'PostgreSQL';
                break;

            case 'sqlsrv':
            case 'sqlserver':
                $platform = 'SQLServer';
                break;

            case 'mysql':
                $platform = 'MySQL';
                break;

            case 'sqlite':
                $platform = 'SQLite';
                break;
        }

        return $platform;
    }

    public static function getShortName(string $platform): string
    {
        switch (strtolower($platform)) {
            case 'postgresql':
                $platform = 'pgsql';
                break;

            case 'sqlserver':
                $platform = 'sqlsrv';
                break;
        }

        return strtolower($platform);
    }

    /**
     * AbstractPlatform constructor.
     *
     * @param  DatabaseAdapter  $db
     */
    public function __construct(DatabaseAdapter $db)
    {
        $this->db = $db;
    }

    public function getGrammar(): AbstractGrammar
    {
        if (!$this->grammar) {
            $this->grammar = $this->createQuery()->getGrammar();
        }

        return $this->grammar;
    }

    public function createQuery(): Query
    {
        return new Query($this->db->getDriver(), $this->name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    abstract public function listDatabasesQuery(): Query;

    abstract public function listSchemaQuery(): Query;

    abstract public function listTablesQuery(?string $schema): Query;

    abstract public function listViewsQuery(?string $schema): Query;

    abstract public function listColumnsQuery(string $table, ?string $schema): Query;

    abstract public function listConstraintsQuery(string $table, ?string $schema): Query;

    abstract public function listIndexesQuery(string $table, ?string $schema): Query;

    /**
     * start
     *
     * @return  static
     */
    public function transactionStart()
    {
        $driver = $this->db->getDriver();

        if ($driver instanceof TransactionDriverInterface) {
            $driver->transactionStart();
        } else {
            $this->db->execute('BEGIN;');
        }

        $this->depth++;

        return $this;
    }

    /**
     * commit
     *
     * @return  static
     */
    public function transactionCommit()
    {
        $driver = $this->db->getDriver();

        if ($driver instanceof TransactionDriverInterface) {
            $driver->transactionCommit();
        } else {
            $this->db->execute('COMMIT;');
        }

        $this->depth--;

        return $this;
    }

    /**
     * rollback
     *
     * @return  static
     */
    public function transactionRollback()
    {
        $driver = $this->db->getDriver();

        if ($driver instanceof TransactionDriverInterface) {
            $driver->transactionRollback();
        } else {
            $this->db->execute('ROLLBACK;');
        }

        $this->depth--;

        return $this;
    }

    /**
     * transaction
     *
     * @param  callable  $callback
     * @param  bool      $autoCommit
     * @param  bool      $enabled
     *
     * @return  static
     *
     * @throws \Throwable
     */
    public function transaction(callable $callback, bool $autoCommit = true, bool $enabled = true)
    {
        if (!$enabled) {
            $callback($this->db, $this);

            return $this;
        }

        $this->transactionStart();

        try {
            $callback($this->db, $this);

            if ($autoCommit) {
                $this->transactionCommit();
            }
        } catch (\Throwable $e) {
            $this->transactionRollback();

            throw $e;
        }

        return $this;
    }
}
