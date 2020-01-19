<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Grammar;

use Windwalker\Query\Clause\Clause;
use Windwalker\Query\Query;

/**
 * The SqlsrvGrammar class.
 */
class SqlsrvGrammar extends Grammar
{
    /**
     * @var string
     */
    protected static $name = 'Sqlsrv';

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
                $table = $elements[array_key_first($elements)];

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
}
