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
 * The SqlitePlatform class.
 */
class SqlitePlatform extends AbstractPlatform
{
    protected $name = 'sqlite';

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
    public function getConstraintKeys(string $constraint, string $table, ?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getTriggerNames(?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getTriggers(?string $schema = null): array
    {
    }
}
