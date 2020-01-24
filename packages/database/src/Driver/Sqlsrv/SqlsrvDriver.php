<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Sqlsrv;

use Windwalker\Database\Driver\AbstractDriver;
use Windwalker\Database\Driver\StatementInterface;

/**
 * The SqlsrvDriver class.
 */
class SqlsrvDriver extends AbstractDriver
{
    protected static $name = 'sqlsrv';

    protected $platformName = 'sqlsrv';

    /**
     * @inheritDoc
     */
    public function prepare($query, array $options = []): StatementInterface
    {
        $conn = $this->connect()->get();

        $query = $this->handleQuery($query, $bounded);

        return new SqlsrvStatement($conn, $query, $bounded);
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(?string $sequence = null): ?string
    {
        return $this->prepare('SELECT @@IDENTITY')->loadResult();
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
        return $this->getPlatform()->getGrammar()->localEscape($value);
    }
}
