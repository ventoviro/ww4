<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Pdo;

use Windwalker\Utilities\Arr;

/**
 * The MysqlConnection class.
 */
class MysqlConnection extends AbstractPdoConnection
{
    protected static $dbtype = 'mysql';

    public function getParameters(array $options): array
    {
        $params['host'] = $options['host'] ?? null;
        $params['port'] = $options['port'] ?? null;
        $params['dbname'] = $options['database'] ?? null;

        $options['dsn'] = $this->getDsn($params);

        return $options;
    }
}
