<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema\Meta;

/**
 * The DatabaseManager class.
 */
class DatabaseManager extends AbstractMetaManager
{
    /**
     * Property tablesCache.
     *
     * @var  array
     */
    protected $schemas = [];

    /**
     * createDatabase
     *
     * @param  array  $options
     *
     * @return static
     */
    public function create(array $options = [])
    {
        $this->db->getSchemaManager()->createDatabase($this->getName(), $options);

        return $this;
    }

    /**
     * dropDatabase
     *
     * @return  static
     */
    public function drop()
    {
        $this->db->getSchemaManager()->dropDatabase($this->getName());

        return $this;
    }

    /**
     * exists
     *
     * @return  boolean
     */
    public function exists()
    {
        return in_array(
            $this->getName(),
            $this->db->listDatabases(),
            true
        );
    }

    /**
     * resetCache
     *
     * @return  static
     */
    public function reset()
    {
        $this->tableCache = [];

        return $this;
    }
}
