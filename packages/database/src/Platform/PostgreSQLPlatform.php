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

use function Windwalker\raw;

/**
 * The PostgresqlPlatform class.
 */
class PostgreSQLPlatform extends AbstractPlatform
{
    protected $name = 'PostgreSQL';

    /**
     * @inheritDoc
     */
    public function getDatabases(): array
    {
        $query = $this->db->getQuery(true)
            ->select('datname')
            ->from('pg_database')
            ->where('datistemplate', '=', raw('false'));

        return $this->db->prepare($query)->loadColumn()->dump();
    }

    /**
     * @inheritDoc
     */
    public function getSchemas(): array
    {
        $query = $this->db->getQuery(true)
            ->select('datname')
            ->from('pg_database')
            ->where('datistemplate', '=', raw('false'));

        return $this->db->prepare($query)->loadColumn()->dump();
    }

    /**
     * @inheritDoc
     */
    public function getTables(?string $schema = null, bool $includeViews = false): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getViews(?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getColumns(string $table, ?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getConstraints(string $table, ?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getConstraintKeys(string $constraint, string $table, ?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getTriggerNames(?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getTriggers(?string $schema = null): array
    {
    }

    /**
     * @inheritDoc
     */
    public function getIndexes(string $table, ?string $schema = null): array
    {
    }

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
        $colNameQuery = $this->createQuery();

        $colNameQuery->select('column_default')
            ->from('information_schema.columns')
            ->where('table_name', $this->db->replacePrefix(trim($table[0], '" ')))
            ->where('column_default', 'LIKE', '%nextval%');

        $colName = $this->db->prepare($colNameQuery)->loadOne()->first();

        $changedColName = str_replace('nextval', 'currval', $colName);

        $insertidQuery = $this->createQuery();

        $insertidQuery->selectRaw($changedColName);

        try {
            return $this->db->prepare($insertidQuery)->loadResult();
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
