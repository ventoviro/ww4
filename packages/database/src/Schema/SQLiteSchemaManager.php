<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema;

/**
 * The SQLiteSchemaManager class.
 */
class SQLiteSchemaManager extends AbstractSchemaManager
{
    /**
     * @inheritDoc
     */
    public function listDatabases(): array
    {
        return $this->db->prepare(
            $this->getPlatform()->listDatabasesQuery()
        )
            ->loadColumn(1)
            ->dump();
    }

    /**
     * @inheritDoc
     */
    public function listSchemas(): array
    {
        return $this->listDatabases();
    }

    /**
     * @inheritDoc
     */
    public function listColumns(string $table, ?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function listConstraints(string $table, ?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function listIndexes(string $table, ?string $schema = null): array
    {
    }
}
