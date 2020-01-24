<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Pgsql;

use Windwalker\Database\Driver\AbstractDriver;
use Windwalker\Database\Driver\StatementInterface;
use Windwalker\Database\Platform\PostgresqlPlatform;

/**
 * The PgsqlDriver class.
 */
class PgsqlDriver extends AbstractDriver
{
    /**
     * @var string
     */
    protected $name = 'pgsql';

    /**
     * @var string
     */
    protected $platformName = 'postgresql';

    /**
     * @inheritDoc
     */
    public function prepare($query, array $options = []): StatementInterface
    {
        $conn = $this->connect()->get();

        $query = $this->handleQuery($query, $bounded);

        return new PgsqlStatement($conn, $query, $bounded);
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(?string $sequence = null): ?string
    {
        /** @var PostgresqlPlatform $platform */
        $platform = $this->getPlatform();

        return $platform->lastInsertId($this->lastQuery, $sequence);
    }

    /**
     * @inheritDoc
     */
    public function quote(string $value): string
    {
        return "'" . $this->escape($value) . "'";
    }

    /**
     * @inheritDoc
     */
    public function escape(string $value): string
    {
        return pg_escape_string($this->connect()->get(), $value);
    }
}
