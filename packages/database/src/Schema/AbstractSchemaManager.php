<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Driver\StatementInterface;
use Windwalker\Database\Platform\AbstractPlatform;
use Windwalker\Database\Schema\Meta\Column;

/**
 * The AbstractSchemaManager class.
 */
abstract class AbstractSchemaManager
{
    protected $platform = '';

    /**
     * @var DatabaseAdapter
     */
    protected $db;

    /**
     * AbstractSchema constructor.
     *
     * @param  DatabaseAdapter  $db
     */
    public function __construct(DatabaseAdapter $db)
    {
        $this->db = $db;
    }

    public static function create(string $platform, DatabaseAdapter $db)
    {
        $class = __NAMESPACE__ . '\\' . AbstractPlatform::getPlatformName($platform) . 'SchemaManager';

        return new $class($db);
    }

    /**
     * @inheritDoc
     */
    public function listDatabases(): array
    {
        return $this->db->prepare(
            $this->getPlatform()->listDatabasesQuery()
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
            $this->getPlatform()->listSchemaQuery()
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
            $this->getPlatform()->listTablesQuery($schema)
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
            $this->getPlatform()->listViewsQuery($schema)
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
            $this->getPlatform()
                ->listColumnsQuery($table, $schema)
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
            $this->getPlatform()
                ->listConstraintsQuery($table, $schema)
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
            $this->getPlatform()
                ->listIndexesQuery($table, $schema)
        );
    }

    /**
     * @return string
     */
    public function getPlatformName(): string
    {
        return $this->platform;
    }

    public function getPlatform(): AbstractPlatform
    {
        return $this->db->getPlatform();
    }

    abstract public function createDatabase(string $name, array $options = []);
    abstract public function dropDatabase();
    abstract public function createSchema();
    abstract public function dropSchema();

    abstract public function createTable();
    abstract public function dropTable(string $table);
    abstract public function renameTable(string $table);
    abstract public function truncateTable(string $table);
    abstract public function getTableDetail(string $table);

    abstract public function addColumn(Column $column);
    abstract public function dropColumn(string $name);
    abstract public function modifyColumn();
    abstract public function renameColumn();

    abstract public function addIndex();
    abstract public function dropIndex();

    abstract public function addConstraint();
    abstract public function dropConstraint();
}
