<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver;

/**
 * The PdoMysqlDriver class.
 */
class PdoMysqlDriver extends PdoDriver
{
    /**
     * @var string
     */
    protected $name = 'pdo_mysql';

    /**
     * @var string
     */
    protected $platformName = 'mysql';
}
