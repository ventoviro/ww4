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
use Windwalker\Database\Driver\StatementInterface;
use Windwalker\Database\Driver\TransactionDriverInterface;
use Windwalker\Database\Platform\Concern\PlatformMetaTrait;
use Windwalker\Database\Platform\Type\DataType;
use Windwalker\Database\Schema\Ddl\Column;
use Windwalker\Database\Schema\Schema;
use Windwalker\Query\Grammar\AbstractGrammar;
use Windwalker\Query\Query;

/**
 * The AbstractPlatform class.
 */
abstract class AbstractPlatform
{
    use PlatformMetaTrait;

    protected ?Query $query = null;

    protected ?AbstractGrammar $grammar = null;

    protected ?DatabaseAdapter $db = null;

    protected ?DataType $dataType = null;

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

    abstract public function listDatabasesQuery(): Query;

    abstract public function listSchemaQuery(): Query;

    abstract public function listTablesQuery(?string $schema): Query;

    abstract public function listViewsQuery(?string $schema): Query;

    abstract public function listColumnsQuery(string $table, ?string $schema): Query;

    abstract public function listConstraintsQuery(string $table, ?string $schema): Query;

    abstract public function listIndexesQuery(string $table, ?string $schema): Query;

    /**
     * @inheritDoc
     */
    public function listDatabases(): array
    {
        return $this->db->prepare(
            $this->listDatabasesQuery()
        )
            ->loadColumn()
            ->dump();
    }

    /**
     * @inheritDoc
     */
    public function listSchemas(): array
    {
        return $this->db->prepare(
            $this->listSchemaQuery()
        )
            ->loadColumn()
            ->dump();
    }

    /**
     * @inheritDoc
     */
    public function listTables(?string $schema = null, bool $includeViews = false): array
    {
        $tables = $this->db->prepare(
            $this->listTablesQuery($schema)
        )
            ->loadColumn()
            ->dump();

        if ($includeViews) {
            $tables = array_merge(
                $tables,
                $this->listViews($schema)
            );
        }

        return $tables;
    }

    /**
     * @inheritDoc
     */
    public function listViews(?string $schema = null): array
    {
        return $this->db->prepare(
            $this->listViewsQuery($schema)
        )
            ->loadColumn()
            ->dump();
    }

    /**
     * @inheritDoc
     */
    abstract public function listColumns(string $table, ?string $schema = null): array;

    /**
     * @inheritDoc
     */
    public function loadColumnsStatement(string $table, ?string $schema = null): StatementInterface
    {
        return $this->db->prepare(
            $this->listColumnsQuery($table, $schema)
        );
    }

    /**
     * @inheritDoc
     */
    abstract public function listConstraints(string $table, ?string $schema = null): array;

    /**
     * @inheritDoc
     */
    public function loadConstraintsStatement(string $table, ?string $schema = null): StatementInterface
    {
        return $this->db->prepare(
            $this->listConstraintsQuery($table, $schema)
        );
    }

    /**
     * @inheritDoc
     */
    abstract public function listIndexes(string $table, ?string $schema = null): array;

    /**
     * @inheritDoc
     */
    public function loadIndexesStatement(string $table, ?string $schema = null): StatementInterface
    {
        return $this->db->prepare(
            $this->listIndexesQuery($table, $schema)
        );
    }

    abstract public function getCurrentDatabase(): ?string;

    public function selectDatabase(string $name): bool
    {
        $this->db->execute('USE ' . $this->db->quoteName($name));

        return true;
    }

    public function createDatabase(string $name, array $options = []): bool
    {
        $this->db->execute(
            $this->getGrammar()
                ::build(
                    'CREATE DATABASE',
                    !empty($options['if_not_exists']) ? 'IF NOT EXISTS' : null,
                    $this->db->quoteName($name)
                )
        );

        return true;
    }

    abstract public function dropDatabase(string $name): bool;
    abstract public function createSchema(string $name, array $options = []): bool;
    abstract public function dropSchema(string $name): bool;

    abstract public function createTable(Schema $schema, bool $ifNotExists = false, array $options = []): bool;
    abstract public function dropTable(string $table, bool $ifExists = false): bool;
    abstract public function renameTable(string $from, string $to): bool;
    abstract public function truncateTable(string $table): bool;
    abstract public function getTableDetail(string $table): array;

    abstract public function addColumn(Column $column): bool;
    abstract public function dropColumn(string $name): bool;
    abstract public function modifyColumn(Column $column): bool;
    abstract public function renameColumn(string $from, string $to): bool;

    abstract public function addIndex(): bool;
    abstract public function dropIndex(): bool;

    abstract public function addConstraint(): bool;
    abstract public function dropConstraint(): bool;

    protected function prepareColumn(Column $column): Column
    {
        $typeMapper = $this->getDataType();

        $type = $typeMapper::getAvailableType($column->getDataType());
        $length = $column->getLengthExpression() ?: $typeMapper::getLength($type);

        $column->length($length);
        $column->dataType($type);

        // Prepare default value
        return $this->prepareDefaultValue($column);
    }

    /**
     * prepareDefaultValue
     *
     * @param Column $column
     *
     * @return  Column
     */
    protected function prepareDefaultValue(Column $column): Column
    {
        $typeMapper = $this->getDataType();

        $default = $column->getColumnDefault();

        if ($default === null && !$column->getIsNullable()) {
            $default = $typeMapper::getDefaultValue($column->getDataType());

            $column->defaultValue($default);
        }

        if ($column->isPrimary() || $column->isAutoIncrement()) {
            $column->defaultValue(false);
        }

        return $column;
    }

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

    public function getDataType(): DataType
    {
        if (!$this->dataType) {
            $class = 'Windwalker\Database\Platform\Type\\' . $this->getName() . 'DataType';

            if (!class_exists($class)) {
                $class = DataType::class;
            }

            $this->dataType = new $class();
        }

        return $this->dataType;
    }
}
