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
use Windwalker\Database\Schema\Ddl\Constraint;
use Windwalker\Database\Schema\Ddl\Index;
use Windwalker\Database\Schema\Schema;
use Windwalker\Query\Clause\AlterClause;
use Windwalker\Query\Clause\Clause;
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
            ->loadAll()
            ->keyBy('TABLE_NAME')
            ->dump(true);

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
        $this->listViewsQuery($schema)->render(true);

        return $this->db->prepare(
            $this->listViewsQuery($schema)
        )
            ->loadAll()
            ->keyBy('TABLE_NAME')
            ->dump(true);
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

    public function selectDatabase(string $name): StatementInterface
    {
        return $this->db->execute('USE ' . $this->db->quoteName($name));
    }

    public function createDatabase(string $name, array $options = []): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()
                ::build(
                    'CREATE DATABASE',
                    !empty($options['if_not_exists']) ? 'IF NOT EXISTS' : null,
                    $this->db->quoteName($name)
                )
        );
    }

    abstract public function dropDatabase(string $name): StatementInterface;
    abstract public function createSchema(string $name, array $options = []): StatementInterface;
    abstract public function dropSchema(string $name): StatementInterface;

    abstract public function createTable(Schema $schema, bool $ifNotExists = false, array $options = []): StatementInterface;

    public function getColumnExpression(Column $column): Clause
    {
        return $this->getGrammar()::build(
            $this->db->quoteName($column->getName()),
            $column->getTypeExpression(),
            $column->getIsNullable() ? '' : 'NOT NULL',
            $column->canHasDefaultValue()
                ? 'DEFAULT ' . $this->db->quote($column->getColumnDefault())
                : '',
            $column->getOption('on_update') ? 'ON UPDATE CURRENT_TIMESTAMP' : null,
            $column->getOption('suffix')
        );
    }

    public function dropTable(string $table, ?string $schema = null, $suffix = null): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()::build(
                'DROP TABLE',
                'IF EXISTS',
                $this->db->quoteName($schema . '.' . $table),
                $suffix
            )
        );
    }

    public function renameTable(string $from, string $to, ?string $schema = null): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()::build(
                'DROP TABLE',
                $this->db->quoteName($schema . '.' . $from),
                'RENAME TO',
                $this->db->quoteName($schema . '.' . $to),
            )
        );
    }

    public function truncateTable(string $table, ?string $schema = null): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()::build(
                'TRUNCATE TABLE',
                $this->db->quoteName($schema . '.' . $table)
            )
        );
    }

    abstract public function getTableDetail(string $table, ?string $schema = null): ?array;

    public function addColumn(string $table, Column $column, ?string $schema = null): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()::build(
                'ALTER TABLE',
                $this->db->quoteName($schema . '.' . $table),
                'ADD COLUMN',
                $this->db->quoteName($column->getName()),
                (string) $this->getColumnExpression($column)
            )
        );
    }

    public function dropColumn(string $table, string $name, ?string $schema = null): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()::build(
                'ALTER TABLE',
                $this->db->quoteName($schema . '.' . $table),
                'DROP COLUMN',
                $this->db->quoteName($name),
            )
        );
    }

    public function modifyColumn(string $table, Column $column, ?string $schema = null): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()::build(
                'ALTER TABLE',
                $this->db->quoteName($schema . '.' . $table),
                'MODIFY COLUMN',
                $this->db->quoteName($column->getName()),
                (string) $this->getColumnExpression($column)
            )
        );
    }

    abstract public function renameColumn(string $table, string $from, string $to, ?string $schema = null): StatementInterface;

    public function addIndex(string $table, Index $index, ?string $schema = null): StatementInterface
    {
        return $this->db->execute(
            $this->db->getQuery(true)
                ->alter('TABLE', $schema . '.' . $table)
                ->tap(fn(AlterClause $alter) => $alter->addIndex(
                    $index->indexName,
                    $this->db->quoteName(array_keys($index->getColumns()))
                ))
        );
    }

    public function dropIndex(string $table, string $name, ?string $schema = null): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()::build(
                'ALTER TABLE',
                $this->db->quoteName($schema . '.' . $table),
                'DROP INDEX',
                $this->db->quoteName($name),
            )
        );
    }

    public function addConstraint(string $table, Constraint $constraint, ?string $schema = null): StatementInterface
    {
        $alter = $this->db->getQuery(true)
            ->alter('TABLE', $schema . '.' . $table);

        if ($constraint->constraintType === Constraint::TYPE_PRIMARY_KEY) {
            $alter->addPrimaryKey(
                null,
                $this->db->quoteName(array_keys($constraint->getColumns()))
            );
        } elseif ($constraint->constraintType === Constraint::TYPE_UNIQUE) {
            $alter->addUniqueKey(
                $constraint->constraintName,
                $this->db->quoteName(array_keys($constraint->getColumns()))
            );
        } elseif ($constraint->constraintType === Constraint::TYPE_FOREIGN_KEY) {
            $alter->addForeignKey(
                $constraint->constraintName,
                $this->db->quoteName(array_keys($constraint->getColumns())),
                $this->db->quoteName(array_keys($constraint->getReferencedColumns())),
                $constraint->updateRule,
                $constraint->deleteRule,
            );
        }

        return $this->db->execute((string) $alter);
    }

    public function dropConstraint(string $table, string $name, ?string $schema = null): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()::build(
                'ALTER TABLE',
                $this->db->quoteName($schema . '.' . $table),
                'DROP CONSTRAINT',
                $this->db->quoteName($name),
            )
        );
    }

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

        if (
            $column->getColumnDefault() === false
            || ($column->getColumnDefault() === null && !$column->getIsNullable())
        ) {
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
