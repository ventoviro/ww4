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
 * The PdoDriver class.
 */
class PdoDriver extends AbstractDriver
{
    /**
     * @var string
     */
    protected $name = 'pdo_odbc';

    /**
     * @var string
     */
    protected $platformName = 'odbc';
}
