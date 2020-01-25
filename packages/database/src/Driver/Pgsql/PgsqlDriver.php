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
use Windwalker\Database\Platform\PgsqlPlatform;

/**
 * The PgsqlDriver class.
 */
class PgsqlDriver extends AbstractDriver
{
    /**
     * @var string
     */
    protected static $name = 'pgsql';

    /**
     * @var string
     */
    protected $platformName = 'pgsql';

    /**
     * @inheritDoc
     */
    public function doPrepare(string $query, array $bounded = [], array $options = []): StatementInterface
    {
        $conn = $this->connect()->get();

        return new PgsqlStatement($conn, $query, $bounded);
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(?string $sequence = null): ?string
    {
        /** @var PgsqlPlatform $platform */
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
