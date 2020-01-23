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
 * The PdoPgsqlConnection class.
 */
class PdoPgsqlConnection extends AbstractPdoConnection
{
    protected static $dbtype = 'pgsql';

    public static function getParameters(array $options): array
    {
        $params['host'] = $options['host'];
        $params['port'] = $options['port'] ?? null;
        $params['dbname'] = $options['database'] ?? null;
        $params['charset'] = $options['charset'] ?? null;

        $options['dsn'] = static::getDsn($params);

        return $options;
    }
}
