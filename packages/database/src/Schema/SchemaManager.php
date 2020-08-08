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

use Windwalker\Database\Schema\Ddl\Table;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

use function PHPUnit\Framework\once;

/**
 * The SchemaManager class.
 */
class SchemaManager extends AbstractMetaManager
{
    use InstanceCacheTrait;

    public function getTable(string $name, bool $new = false): TableManager
    {
        return $this->once('table.manager.' . $name, fn () => new TableManager($name, $this->db), $new);
    }

    /**
     * @param  bool  $includeViews
     * @param  bool  $refresh
     *
     * @return  Table[]
     */
    public function getTables(bool $includeViews = false, bool $refresh = false): array
    {
        $platform = $this->db->getPlatform();

        $tables = $this->once(
            'tables',
            fn () => Table::wrapList(
                $platform->listTables($this->getName())
            ),
            $refresh
        );

        if ($includeViews) {
            $tables = array_merge($tables, $this->getViews());
        }

        return $tables;
    }

    public function getViews(bool $refresh = false)
    {
        return $this->once(
            'views',
            fn () => Table::wrapList(
                $this->getPlatform()->listViews($this->getName())
            ),
            $refresh
        );
    }

    public function getTableDetail(string $table, bool $includeViews = false): ?Table
    {
        return $this->getTables($includeViews)[$table];
    }

    public function hasTable(string $table): bool
    {
        return in_array($this->db->replacePrefix($table), $this->getTables(), true);
    }

    public function reset(): static
    {
        $this->tables = null;
        $this->views = null;

        return $this;
    }
}
