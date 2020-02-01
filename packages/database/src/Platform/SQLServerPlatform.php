<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform;

/**
 * The SqlserverPlatform class.
 */
class SQLServerPlatform extends AbstractPlatform
{
    protected $name = 'SQLServer';

    /**
     * @inheritDoc
     */
    public function getDatabases(): array
    {
        return $this->db->prepare(
            $this->db->getQuery(true)
                ->select('name')
                ->from('master.dbo.sysdatabases')
        )
            ->loadColumn()
            ->dump();
    }

    /**
     * @inheritDoc
     */
    public function getSchemas(): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getTables(?string $schema = null, bool $includeViews = false): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getViews(?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getColumns(string $table, ?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getConstraints(string $table, ?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getIndexes(string $table, ?string $schema = null): array
    {
    }
}
