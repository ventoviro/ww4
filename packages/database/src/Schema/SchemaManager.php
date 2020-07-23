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
use Windwalker\Database\Manager\TableManager;

/**
 * The SchemaManager class.
 */
class SchemaManager extends AbstractMetaManager
{
    /**
     * @var array
     */
    protected $tables = null;

    /**
     * @var array
     */
    protected $views = null;

    /**
     * getTable
     *
     * @param string $name
     * @param bool   $new
     *
     * @return  TableManager
     */
    public function getTable(string $name, bool $new = false)
    {
        return $this->db->getTable($this->getName() . '.' . $name, $new);
    }

    /**
     * Method to get an array of all tables in the database.
     *
     * @param  bool  $includeViews
     * @param  bool  $refresh
     *
     * @return  array  An array of all the tables in the database.
     *
     * @since   2.0
     */
    public function getTables(bool $includeViews = false, bool $refresh = false): array
    {
        $schemaManager = $this->db->getPlatform();

        if ($this->tables === null || $refresh) {
            $this->tables = $schemaManager->listTables($this->getName());
        }

        $tables = $this->tables;

        if ($includeViews) {
            if ($this->views === null || $refresh) {
                $this->views = $schemaManager->listViews($this->getName());
            }

            array_merge($tables, $this->views);
        }

        return $tables;
    }

    /**
     * getTableDetail
     *
     * @param string $table
     *
     * @return  array
     */
    public function getTableDetail(string $table): array
    {
        return $this->db->getPlatform()->getTableDetail($table);
    }

    /**
     * tableExists
     *
     * @param string $table
     *
     * @return  bool
     */
    public function hasTable(string $table): bool
    {
        return in_array($this->db->replacePrefix($table), $this->getTables(), true);
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        $this->tables = null;
        $this->views = null;

        return $this;
    }
}
