<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Handler;

use Windwalker\Database\Platform\AbstractPlatform;

/**
 * The PdoHandler class.
 */
class PdoHandler
{
    /**
     * Returns a merge/upsert (i.e. insert or update) SQL query when supported by the database.
     *
     * @return string|null The SQL string or null when not supported
     */
    private function getMergeSql()
    {
        $platformName = $this->db->getPlatform()->getName();

        $columns = $this->getOption('columns');
        $table = $this->getOption('table');

        switch ($platformName) {
            case AbstractPlatform::MYSQL:
                return <<<SQL
INSERT INTO {$table} ({$columns['id_col']},
{$columns['data_col']},
{$columns['time_col']})
 VALUES (:id, :data, :time)
 ON DUPLICATE KEY UPDATE {$columns['data_col']} = VALUES({$columns['data_col']}),
 {$columns['time_col']} = VALUES({$columns['time_col']})
SQL;

            case 'oci':
                // DUAL is Oracle specific dummy table
                return <<<SQL
 MERGE INTO {$table} USING DUAL ON ({$columns['id_col']} = :id)
 WHEN NOT MATCHED THEN INSERT ({$columns['id_col']},
 {$columns['data_col']},
 {$columns['time_col']})
 VALUES (:id, :data, :time)
 WHEN MATCHED THEN UPDATE SET {$columns['data_col']} = :data, {$columns['time_col']} = :time
SQL;

            case 'sqlsrv' === $platformName && version_compare(
                    $this->db->getAttribute(\PDO::ATTR_SERVER_VERSION),
                    '10',
                    '>='
                ):
                // @codingStandardsIgnoreStart
                // MERGE is only available since SQL Server 2008 and must be terminated by semicolon
                // It also requires HOLDLOCK according to http://weblogs.sqlteam.com/dang/archive/2009/01/31/UPSERT-Race-Condition-With-MERGE.aspx
                return "MERGE INTO {$table} WITH (HOLDLOCK) USING (SELECT 1 AS dummy) AS src ON ({$columns['id_col']} = :id) " .
                    "WHEN NOT MATCHED THEN INSERT ({$columns['id_col']}, {$columns['data_col']}, {$columns['time_col']}) VALUES (:id, :data, :time) " .
                    "WHEN MATCHED THEN UPDATE SET {$columns['data_col']} = :data, {$columns['time_col']} = :time;";

            case 'sqlite':
                return "INSERT OR REPLACE INTO {$table} ({$columns['id_col']}, {$columns['data_col']}, {$columns['time_col']}) VALUES (:id, :data, :time)";
        }

        // @codingStandardsIgnoreEnd
        return '';
    }
}
