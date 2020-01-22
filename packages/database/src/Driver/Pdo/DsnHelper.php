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
 * The PdoHelper class.
 */
class DsnHelper
{
    /**
     * extractDsn
     *
     * @param   string $dsn
     *
     * @return  array
     */
    public static function extract(string $dsn): array
    {
        // Parse DSN to array
        $dsn = str_replace(';', "\n", $dsn);

        $values = [];

        foreach (explode("\n", $dsn) as $value) {
            [$k, $v] = explode('=', trim($value));

            $values[$k] = $v;
        }

        return $values;
    }

    public static function build(array $params, ?string $dbtype = null): string
    {
        $dsn = [];

        foreach ($params as $key => $value) {
            $params[] = $key . '=' . $value;
        }

        $dsn = implode(';', $dsn);

        if ($dbtype) {
            $dsn = $dbtype . ':' . $dsn;
        }

        return $dsn;
    }
}
