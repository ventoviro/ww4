<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Mysqli;

use Windwalker\Database\Driver\AbstractDriver;
use Windwalker\Database\Driver\StatementInterface;

/**
 * The MysqliDriver class.
 */
class MysqliDriver extends AbstractDriver
{
    protected static $name = 'mysqli';

    /**
     * @var string
     */
    protected $platformName = 'mysql';

    /**
     * @inheritDoc
     */
    public function prepare($query, array $options = []): StatementInterface
    {
        $conn = $this->connect()->get();

        $query = $this->handleQuery($query, $bounded);

        return new MysqliStatement($conn, $query, $bounded);
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(?string $sequence = null): ?string
    {
        /** @var \mysqli $mysqli */
        $mysqli = $this->connect()->get();

        return (string) $mysqli->insert_id;
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
        /** @var \mysqli $mysqli */
        $mysqli = $this->connect()->get();

        return $mysqli->real_escape_string($value);
    }
}
