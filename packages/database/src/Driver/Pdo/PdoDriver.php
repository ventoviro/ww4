<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver\Pdo;

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Driver\AbstractDriver;

/**
 * The PdoDriver class.
 */
class PdoDriver extends AbstractDriver
{
    /**
     * @var string
     */
    protected $name = 'pdo';

    /**
     * @var string
     */
    protected $platformName = 'odbc';

    /**
     * @inheritDoc
     */
    public function __construct(DatabaseAdapter $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function connect()
    {
    }

    /**
     * @inheritDoc
     */
    public function disconnect()
    {
    }
}
