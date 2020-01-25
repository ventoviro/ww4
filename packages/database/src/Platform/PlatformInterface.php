<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform;

use Windwalker\Query\Grammar\Grammar;
use Windwalker\Query\Query;

/**
 * Interface PlatformInterface
 */
interface PlatformInterface
{
    public const DEFAULT_SCHEMA = '__DEFAULT_SCHEMA__';

    /**
     * getGrammar
     *
     * @return  Grammar
     */
    public function getGrammar(): Grammar;

    /**
     * createQuery
     *
     * @return  Query
     */
    public function createQuery(): Query;

    /**
     * Get schemas.
     *
     * @return string[]
     */
    public function getSchemas(): array;

    /**
     * Get tables.
     *
     * @param null|string $schema
     * @param bool $includeViews
     * @return array
     */
    public function getTables(?string $schema = null, bool $includeViews = false): array;

    /**
     * Get views
     *
     * @param null|string $schema
     * @return array
     */
    public function getViews(?string $schema = null): array;

    /**
     * Get columns
     *
     * @param string $table
     * @param null|string $schema
     * @return array
     */
    public function getColumns(string $table, ?string $schema = null): array;

    /**
     * Get constraints
     *
     * @param string $table
     * @param null|string $schema
     * @return array
     */
    public function getConstraints(string $table, ?string $schema = null): array;

    /**
     * Get trigger names
     *
     * @param null|string $schema
     * @return string[]
     */
    public function getTriggerNames(?string $schema = null): array;

    /**
     * Get triggers
     *
     * @param null|string $schema
     * @return array
     */
    public function getTriggers(?string $schema = null): array;
}
