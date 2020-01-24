<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver\Pdo;

use Windwalker\Database\Test\Driver\AbstractDriverTest;

/**
 * The PdoSqlsrvDriverTest class.
 */
class PdoSqlsrvDriverTest extends AbstractDriverTest
{
    protected static $platform = 'sqlsrv';

    protected static $driverName = 'pdo_sqlsrv';

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        // Fix for MacOS ODBC driver 17.2 issue
        // @see https://github.com/Microsoft/msphpsql/issues/909
        setlocale(LC_ALL, 'en_GB');

        parent::setUpBeforeClass();
    }
}
