<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Grammar;

use Windwalker\Query\Query;

/**
 * The SqlsrvGrammar class.
 */
class SQLServerGrammar extends Grammar
{
    /**
     * @var string
     */
    protected static $name = 'SQLServer';

    /**
     * @var array
     */
    protected static $nameQuote = ['[', ']'];

    /**
     * @var string
     */
    protected static $nullDate = '1900-01-01 00:00:00';

    public function compileInsert(Query $query): string
    {
        $sql['insert'] = $query->getInsert();

        if ($set = $query->getSet()) {
            $sql['set'] = $set;
        } else {
            if ($columns = $query->getColumns()) {
                $sql['columns'] = $columns;
            }

            if ($values = $query->getValues()) {
                $sql['values'] = $values;
            }

            if ($query->getIncrementField()) {
                $elements = $sql['insert']->getElements();
                $table    = $elements[array_key_first($elements)];

                $sql = array_merge(
                    ['id_insert_on' => sprintf('SET IDENTITY_INSERT %s ON;', $table)],
                    $sql,
                    ['id_insert_off' => sprintf('; SET IDENTITY_INSERT %s OFF;', $table)]
                );
            }
        }

        return trim(implode(' ', $sql));
    }

    public function compileLimit(Query $query, array $sql): array
    {
        $limit  = $query->getLimit();
        $offset = (int) $query->getOffset();

        $q = implode(' ', $sql);

        if ($limit !== null) {
            $total = $offset + $limit;

            $position = stripos($q, 'SELECT');
            $distinct = stripos($q, 'SELECT DISTINCT');

            if ($position === $distinct) {
                $q = substr_replace($q, 'SELECT DISTINCT TOP ' . (int) $total, $position, 15);
            } else {
                $q = substr_replace($q, 'SELECT TOP ' . (int) $total, $position, 6);
            }
        }

        if (!$offset) {
            return [$q];
        }

        return array_merge(
            ['row_number' => 'SELECT * FROM (SELECT *, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) AS RowNumber FROM ('],
            [$q],
            ['end_row_number' => ') AS A) AS A WHERE RowNumber > ' . (int) $offset]
        );
    }

    /**
     * If no connection set, we escape it with default function.
     *
     * @see https://stackoverflow.com/a/2526717
     *
     * @param  string  $text
     *
     * @return  string
     */
    public function localEscape(string $text): string
    {
        if ($text === '') {
            return $text;
        }

        if (is_numeric($text)) {
            return $text;
        }

        $nonDisplayables = [
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        ];

        foreach ($nonDisplayables as $regex) {
            $text = preg_replace($regex, '', $text);
        }

        return str_replace("'", "''", $text);
    }

    /**
     * @inheritDoc
     */
    public function listDatabases($where = null): Query
    {
        return $this->createQuery()
            ->select('name')
            ->from('master.dbo.sysdatabases');
    }

    /**
     * @inheritDoc
     */
    public function listTables(?string $dbname): Query
    {
        $query = $this->createQuery()
            ->select('TABLE_NAME')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_TYPE', 'BASE TABLE');

        if ($dbname !== null) {
            $query->where('TABLE_CATALOG', $dbname);
        } else {
            $query->where('TABLE_SCHEMA', '!=', 'INFORMATION_SCHEMA');
        }

        return $query;
    }
}
