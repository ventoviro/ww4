<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Sqlsrv;

use Windwalker\Database\Driver\AbstractConnection;
use Windwalker\Database\Exception\DbConnectException;

/**
 * The SqlsrvConnection class.
 */
class SqlsrvConnection extends AbstractConnection
{
    protected static $name = 'sqlsrv';

    /**
     * @inheritDoc
     */
    public static function isSupported(): bool
    {
        return extension_loaded('sqlsrv');
    }

    public static function getParameters(array $options): array
    {
        $params = [];

        $params['Database']     = $options['database'] ?? null;
        $params['UID']          = $options['username'] ?? null;
        $params['PWD']          = $options['password'] ?? null;
        $params['CharacterSet'] = $options['charset'] ?? null;

        $params            = array_filter($params);
        $options['params'] = $params;

        return $options;
    }

    protected function doConnect(array $options)
    {
        $conn = sqlsrv_connect(
            $options['host'],
            $options['params']
        );

        if (!$conn) {
            $errors = sqlsrv_errors();

            throw new DbConnectException(
                sprintf(
                    'SQLSTATE: %s Message: %s',
                    $errors[0]['SQLSTATE'],
                    $errors[0]['message']
                ),
                $errors[0]['code']
            );
        }

        return $conn;
    }

    /**
     * @inheritDoc
     */
    public function disconnect()
    {
        if (!$this->isConnected()) {
            return true;
        }

        $r = sqlsrv_close($this->connection);

        $this->connection = null;

        return $r;
    }
}
