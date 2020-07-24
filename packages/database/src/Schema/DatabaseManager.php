<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Schema;

use Windwalker\Database\Manager\AbstractMetaManager;

/**
 * The DatabaseManager class.
 */
class DatabaseManager extends AbstractMetaManager
{
    /**
     * select
     *
     * @return  static
     */
    public function select()
    {
        $this->db->getPlatform()->selectDatabase($this->getName());

        return $this;
    }

    /**
     * createDatabase
     *
     * @param  bool   $ifNotExists
     * @param  array  $options
     *
     * @return static
     */
    public function create(bool $ifNotExists = false, array $options = [])
    {
        if ($ifNotExists && in_array($this->getName(), $this->db->listDatabases(), true)) {
            return $this;
        }

        $this->db->getPlatform()->createDatabase($this->getName(), $options);

        return $this;
    }

    /**
     * dropDatabase
     *
     * @param  bool  $ifExists
     *
     * @return  static
     */
    public function drop(bool $ifExists = false)
    {
        $name = $this->getName();

        if ($ifExists && !in_array($name, $this->db->listDatabases(), true)) {
            return $this;
        }

        if ($name === $this->db->getPlatform()->getCurrentDatabase()) {
            $this->db->disconnect();

            $this->db->setOption('database', null);
        }

        $this->db->getPlatform()->dropDatabase($name);

        return $this;
    }

    /**
     * exists
     *
     * @return  bool
     */
    public function exists(): bool
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
    public function reset(): static
    {
        return $this;
    }
}
