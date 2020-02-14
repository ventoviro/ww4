<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Manager;

use Windwalker\Database\DatabaseAdapter;

/**
 * The AbstractDbManager class.
 */
class AbstractDbManager
{
    /**
     * @var DatabaseAdapter
     */
    protected $db;

    /**
     * AbstractDbManager constructor.
     *
     * @param  DatabaseAdapter  $db
     */
    public function __construct(DatabaseAdapter $db)
    {
        $this->db = $db;
    }

    /**
     * @return DatabaseAdapter
     */
    public function getDb(): DatabaseAdapter
    {
        return $this->db;
    }
}
