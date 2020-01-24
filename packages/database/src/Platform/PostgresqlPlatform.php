<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Platform;

use Windwalker\Database\Driver\Pdo\PdoDriver;
use Windwalker\Query\Query;

/**
 * The PostgresqlPlatform class.
 */
class PostgresqlPlatform extends AbstractPlatform
{
    public function lastInsertId($insertQuery, ?string $sequence = null): ?string
    {
        if ($sequence && $this->db->getDriver() instanceof PdoDriver) {
            /** @var \PDO $pdo */
            $pdo = $this->db->getDriver()->getConnection()->get();
            return $pdo->lastInsertId($sequence);
        }

        if ($insertQuery instanceof Query) {
            $table = $insertQuery->getInsert()->getElements();
        } else {
            preg_match('/insert\s*into\s*[\"]*(\W\w+)[\"]*/i', $insertQuery, $matches);

            if (!isset($matches[1])) {
                return null;
            }

            $table = [$matches[1]];
        }

        /* find sequence column name */
        $colNameQuery = $this->getQuery();

        $colNameQuery->select('column_default')
            ->from('information_schema.columns')
            ->where('table_name', $this->db->replacePrefix(trim($table[0], '" ')))
            ->where('column_default', 'LIKE', '%nextval%');

        $colName = $this->db->getDriver()->prepare($colNameQuery)->loadOne()->first();

        $changedColName = str_replace('nextval', 'currval', $colName);

        $insertidQuery = $this->getQuery();

        $insertidQuery->selectRaw($changedColName);
        show($insertidQuery->render(true));
        try {
            return $this->db->getDriver()->prepare($insertidQuery)->loadResult();
        } catch (\PDOException $e) {
            // 55000 means we trying to insert value to serial column
            // Just return because insertedId get the last generated value.
            if ($e->getCode() !== 55000) {
                throw $e;
            }
        }

        return null;
    }
}
