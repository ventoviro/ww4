<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema\Meta;

use Windwalker\Database\DatabaseAdapter;

/**
 * The AbstractDbManager class.
 */
class AbstractMetaManager
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var DatabaseAdapter
     */
    protected $db;

    /**
     * AbstractDbManager constructor.
     *
     * @param  string           $name
     * @param  DatabaseAdapter  $db
     */
    public function __construct(string $name, DatabaseAdapter $db)
    {
        $this->db = $db;
        $this->name = $name;
    }

    /**
     * @return DatabaseAdapter
     */
    public function getDb(): DatabaseAdapter
    {
        return $this->db;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * reset
     *
     * @return  static
     */
    abstract public function reset();
}
