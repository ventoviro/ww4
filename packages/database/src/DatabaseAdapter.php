<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database;

use Windwalker\Database\Driver\AbstractDriver;
use Windwalker\Database\Driver\ConnectionInterface;
use Windwalker\Database\Driver\DriverFactory;
use Windwalker\Database\Driver\StatementInterface;
use Windwalker\Database\Manager\TableManager;
use Windwalker\Database\Manager\WriterManager;
use Windwalker\Database\Platform\AbstractPlatform;
use Windwalker\Database\Schema\DatabaseManager;
use Windwalker\Database\Schema\SchemaManager;
use Windwalker\Event\EventListenableInterface;
use Windwalker\Event\EventListenableTrait;
use Windwalker\Query\Query;
use Windwalker\Utilities\Cache\InstanceCacheTrait;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The DatabaseAdapter class.
 *
 * @method Query select(...$columns)
 * @method Query update(string $table, ?string $alias = null)
 * @method Query delete(string $table, ?string $alias = null)
 * @method Query insert(string $table, ?string $incrementField = null)
 */
class DatabaseAdapter implements EventListenableInterface
{
    use OptionAccessTrait;
    use EventListenableTrait;
    use InstanceCacheTrait;

    protected ?AbstractDriver $driver = null;

    protected \Stringable|string|null $query = null;

    /**
     * DatabaseAdapter constructor.
     *
     * @param  array  $options
     */
    public function __construct(array $options = [])
    {
        $this->prepareOptions(
            [
                'driver' => '',
                'host' => 'localhost',
                'database' => null,
                'username' => null,
                'password' => null,
                'port' => null,
                'prefix' => null,
                'charset' => null,
                'driverOptions' => [],
            ],
            $options
        );
    }

    public function connect(): ConnectionInterface
    {
        return $this->getDriver()->connect();
    }

    public function disconnect()
    {
        return $this->getDriver()->disconnect();
    }

    public function prepare($query, array $options = []): StatementInterface
    {
        $this->query = $query;

        return $this->getDriver()->prepare($query, $options);
    }

    public function execute($query, ?array $params = null): StatementInterface
    {
        $this->query = $query;

        return $this->getDriver()->execute($query, $params);
    }

    /**
     * getQuery
     *
     * @param  bool  $new
     *
     * @return string|Query
     */
    public function getQuery(bool $new = false): Query|\Stringable|string|null
    {
        if ($new) {
            return $this->getPlatform()->createQuery();
        }

        return $this->query;
    }

    public function createQuery(): Query
    {
        return $this->getQuery(true);
    }

    public function getCachedQuery(bool $new = false): Query
    {
        return $this->once('cached.query', fn () => $this->getQuery(true), $new);
    }

    /**
     * quoteName
     *
     * @param  mixed  $value
     *
     * @return  array|string
     */
    public function quoteName(mixed $value): array|string
    {
        return $this->getPlatform()->getGrammar()::quoteNameMultiple($value);
    }

    public function quote(mixed $value): array|string
    {
        return $this->getCachedQuery()->quote($value);
    }

    /**
     * listDatabases
     *
     * @return  array
     */
    public function listDatabases(): array
    {
        return $this->getPlatform()->listDatabases();
    }

    /**
     * listDatabases
     *
     * @return  array
     */
    public function listSchemas(): array
    {
        return $this->getPlatform()->listSchemas();
    }

    /**
     * listTables
     *
     * @param  string|null  $schema
     * @param  bool         $includeViews
     *
     * @return  array
     */
    public function listTables(?string $schema = null, bool $includeViews = false): array
    {
        return $this->getSchema($schema)->getTables($includeViews);
    }

    /**
     * @return AbstractDriver
     */
    public function getDriver(): AbstractDriver
    {
        if (!$this->driver) {
            $this->driver = DriverFactory::create($this->getOption('driver'), $this);
        }

        return $this->driver;
    }

    /**
     * @return AbstractPlatform
     */
    public function getPlatform(): AbstractPlatform
    {
        return $this->getDriver()->getPlatform();
    }

    public function getDatabase(string $name = null, $new = false): DatabaseManager
    {
        $name = $name ?? $this->getOption('database');

        return $this->once('database.' . $name, fn () => new DatabaseManager($name, $this), $new);
    }

    public function getSchema(?string $name = null, $new = false): SchemaManager
    {
        $name = $name ?? $this->getPlatform()::getDefaultSchema();

        return $this->once('schema.' . $name, fn () => new SchemaManager($name, $this), $new);
    }

    public function getTable(string $name, $new = false): TableManager
    {
        return $this->getSchema()->getTable($name, $new);
    }

    public function getWriter($new = false): WriterManager
    {
        return $this->once('writer', fn () => new WriterManager($this), $new);
    }

    public function replacePrefix(string $query, string $prefix = '#__'): string
    {
        return $this->getDriver()->replacePrefix($query, $prefix);
    }

    /**
     * transaction
     *
     * @param  callable  $callback
     * @param  bool      $autoCommit
     * @param  bool      $enabled
     *
     * @return  mixed
     *
     * @throws \Throwable
     */
    public function transaction(callable $callback, bool $autoCommit = true, bool $enabled = true)
    {
        return $this->getPlatform()->transaction($callback, $autoCommit, $enabled);
    }

    /**
     * getDateFormat
     *
     * @return  string
     */
    public function getDateFormat(): string
    {
        return $this->getPlatform()->getGrammar()::dateFormat();
    }

    /**
     * getNullDate
     *
     * @return  string
     */
    public function getNullDate(): string
    {
        return $this->getPlatform()->getGrammar()::nullDate();
    }

    public function isNullDate(string|int|null|\DateTimeInterface $date): bool
    {
        if (is_numeric($date)) {
            $date = new \DateTime($date);
        }

        if ($date instanceof \DateTimeInterface) {
            $date = $date->format($this->getDateFormat());
        }

        return in_array(
            $date,
            [
                $this->getNullDate(),
                '0000-00-00 00:00:00',
                '',
                null,
            ],
            true
        );
    }

    public function __call(string $name, array $args)
    {
        $queryMethods = [
            'select',
            'delete',
            'update',
            'insert'
        ];

        if (in_array(strtolower($name), $queryMethods, true)) {
            return $this->createQuery()->$name(...$args);
        }

        throw new \BadMethodCallException(
            sprintf('Call to undefined method of: %s::%s()', static::class, $name)
        );
    }
}
