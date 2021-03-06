<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Pdo;

/**
 * The MysqlConnection class.
 */
class PdoMysqlConnection extends AbstractPdoConnection
{
    protected static $dbtype = 'mysql';

    public static function getParameters(array $options): array
    {
        $params['host'] = $options['host'] ?? null;
        $params['port'] = $options['port'] ?? null;
        $params['dbname'] = $options['database'] ?? null;
        $params['charset'] = $options['charset'] ?? null;

        $options['dsn'] = static::getDsn($params);

        return $options;
    }
}
