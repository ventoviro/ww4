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
 * The OdbcConnection class.
 */
class OdbcConnection extends AbstractPdoConnection
{
    /**
     * @var string
     */
    protected static $dbtype = 'odbc';

    public function getDsnParameters(array $options): array
    {
        $params = [];

        if ($options['host'] ?? null) {
            // $params['host']
        }
    }
}
