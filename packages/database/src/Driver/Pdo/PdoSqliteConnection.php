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
 * The PdoSqliteConnection class.
 */
class PdoSqliteConnection extends AbstractPdoConnection
{
    protected static $dbtype = 'sqlite';

    public static function getParameters(array $options): array
    {
        $options['dsn'] = static::$dbtype . ':' . ($options['database'] ?? $options['file']);

        return $options;
    }
}
