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

use function Windwalker\Query\clause;

/**
 * The AbstractPlatform class.
 */
abstract class AbstractPlatform
{
    public const MYSQL = 'MySQL';
    public const POSTGRESQL = 'PostgreSQL';
    public const SQLSERVER = 'SQLServer';
    public const SQLITE = 'SQLite';

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var string|null
     */
    protected static ?string $defaultSchema = null;

    protected int $depth = 0;

    protected ?Query $query = null;

    protected ?AbstractGrammar $grammar = null;

    protected ?DatabaseAdapter $db = null;

    protected ?DataType $dataType = null;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public static function create(string $platform, DatabaseAdapter $db)
    {
        $class = __NAMESPACE__ . '\\' . static::getPlatformName($platform) . 'Platform';

        return new $class($db);
    }

    /**
     * @return string
     */
    public static function getDefaultSchema(): ?string
    {
        return static::$defaultSchema;
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

    public function listDatabases(): array
    {
        return $this->db->prepare(
            $this->listDatabasesQuery()
        )
            ->loadColumn()
            ->dump();
    }

    public function listSchemas(): array
    {
        return $this->listSchemaQuery()
            ->loadColumn()
            ->dump();
    }

    /**
     * @inheritDoc
     */
    public function listTables(?string $schema = null, bool $includeViews = false): array
    {
        $tables = $this->listTablesQuery($schema)
            ->all()
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

        return $this->listViewsQuery($schema)
            ->all()
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
        return $this->listColumnsQuery($table, $schema)->getIterator();
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
        return $this->listConstraintsQuery($table, $schema)->getIterator();
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
        return $this->listIndexesQuery($table, $schema)->getIterator();
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

    public function dropDatabase(string $name, array $options = []): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()
                ::build(
                    'DROP DATABASE',
                    !empty($options['if_exists']) ? 'IF EXISTS' : null,
                    $this->db->quoteName($name)
                )
        );
    }

    public function createSchema(string $name, array $options = []): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()
                ::build(
                    'CREATE SCHEMA',
                    !empty($options['if_not_exists']) ? 'IF NOT EXISTS' : null,
                    $this->db->quoteName($name)
                )
        );
    }

    /**
     * dropSchema
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return  StatementInterface
     */
    public function dropSchema(string $name, array $options = []): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()
                ::build(
                    'DROP SCHEMA',
                    $this->db->quoteName($name)
                )
        );
    }

    abstract public function createTable(Schema $schema, bool $ifNotExists = false, array $options = []): StatementInterface;

    public function getColumnExpression(Column $column): Clause
    {
        return $this->getGrammar()::build(
            $column->getTypeExpression(),
            $column->getIsNullable() ? '' : 'NOT NULL',
            $column->canHasDefaultValue()
                ? 'DEFAULT ' . $this->db->quote($column->getColumnDefault())
                : '',
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
                'ALTER TABLE',
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

    public function getTableDetail(string $table, ?string $schema = null): ?array
    {
        return $this->listTables($schema, true)[$table] ?? null;
    }

    public function addColumn(string $table, Column $column, ?string $schema = null): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()::build(
                'ALTER TABLE',
                $this->db->quoteName($schema . '.' . $table),
                'ADD COLUMN',
                $this->db->quoteName($column->getColumnName()),
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
                $this->db->quoteName($column->getColumnName()),
                (string) $this->getColumnExpression($column)
            )
        );
    }

    abstract public function renameColumn(string $table, string $from, string $to, ?string $schema = null): StatementInterface;

    public function addIndex(string $table, Index $index, ?string $schema = null): StatementInterface
    {
        return $this->db->execute(
            $this->getGrammar()::build(
                'CREATE INDEX',
                $this->db->quoteName($index->indexName),
                'ON',
                $this->db->quoteName($schema . '.' . $table),
                (string) clause('()', $this->prepareKeyColumns($index->getColumns()), ','),
            )
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
        $alter = $this->db->createQuery()
            ->alter('TABLE', $schema . '.' . $table);

        if ($constraint->constraintType === Constraint::TYPE_PRIMARY_KEY) {
            $alter->addPrimaryKey(
                $constraint->constraintName,
                $this->prepareKeyColumns($constraint->getColumns())
            );
        } elseif ($constraint->constraintType === Constraint::TYPE_UNIQUE) {
            $alter->addUniqueKey(
                $constraint->constraintName,
                $this->prepareKeyColumns($constraint->getColumns())
            );
        } elseif ($constraint->constraintType === Constraint::TYPE_FOREIGN_KEY) {
            $alter->addForeignKey(
                $constraint->constraintName,
                $this->prepareKeyColumns($constraint->getColumns()),
                $this->prepareKeyColumns($constraint->getReferencedColumns()),
                $constraint->updateRule,
                $constraint->deleteRule,
            );
        }

        return $this->db->execute((string) $alter);
    }

    protected function prepareKeyColumns(array $columns): array
    {
        return array_map(fn (Column $col) => $this->getKeyColumnExpression($col), $columns);
    }

    protected function getKeyColumnExpression(Column $column): Clause
    {
        $expr = clause($this->db->quoteName($column->getColumnName()), [], ' ');

        if ($column->getOption('sort')) {
            $expr->append($column->getOption('sort'));
        }

        return $expr;
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

    public function prepareColumn(Column $column): Column
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
    public function prepareDefaultValue(Column $column): Column
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
     * @return  mixed
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
            $result = $callback($this->db, $this);

            if ($autoCommit) {
                $this->transactionCommit();
            }

            return $result;
        } catch (\Throwable $e) {
            $this->transactionRollback();

            throw $e;
        }
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
