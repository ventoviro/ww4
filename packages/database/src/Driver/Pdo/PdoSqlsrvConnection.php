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
 * The PdoSqlsrvConnection class.
 */
class PdoSqlsrvConnection extends AbstractPdoConnection
{
    protected static $dbtype = 'sqlsrv';

    protected static $defaultAttributes = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
        \PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE => true
    ];

    public static function getParameters(array $options): array
    {
        $params['Server'] = $options['host'];

        if (isset($params['port'])) {
            $params['Server'] .= ',' . $params['port'];
        }

        $params['Database']     = $options['database'] ?? null;
        $params['CharacterSet'] = $options['charset'] ?? null;
        $params['MultipleActiveResultSets'] = 'False';

        $options['dsn'] = static::getDsn($params);

        return $options;
    }
}
