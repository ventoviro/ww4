<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Mysqli;

use Windwalker\Database\Driver\AbstractConnection;
use Windwalker\Database\Exception\DatabaseConnectException;

/**
 * The MysqliConnection class.
 */
class MysqliConnection extends AbstractConnection
{
    protected static $name = 'mysqli';

    /**
     * @var \mysqli
     */
    protected $connection;

    /**
     * @inheritDoc
     */
    public static function isSupported(): bool
    {
        return extension_loaded('mysqli');
    }

    public static function getParameters(array $options): array
    {
        return $options;
    }

    protected function doConnect(array $options)
    {
        mysqli_report(MYSQLI_REPORT_ALL | MYSQLI_REPORT_STRICT);

        return mysqli_connect(
            $options['host'] ?? null,
            $options['username'] ?? null,
            $options['password'] ?? null,
            $options['database'] ?? null
        );
    }

    /**
     * @inheritDoc
     */
    public function disconnect()
    {
        if (!$this->isConnected()) {
            return true;
        }

        $r = $this->connection->close();

        $this->connection = null;

        return $r;
    }

    /**
     * @return \mysqli
     */
    public function get(): ?\mysqli
    {
        return parent::get();
    }
}
