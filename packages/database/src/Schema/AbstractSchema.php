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

/**
 * The AbstractSchemaManager class.
 */
abstract class AbstractSchema
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
        $class = __NAMESPACE__ . '\\' . AbstractPlatform::getPlatformName($platform) . 'Schema';

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
}
