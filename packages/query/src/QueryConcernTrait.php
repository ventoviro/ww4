<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

use Windwalker\Utilities\TypeCast;

/**
 * Trait QueryHelperTrait
 */
trait QueryConcernTrait
{
    /**
     * convertArrayToWheres
     *
     * @param  Query  $query
     * @param  mixed  $wheres
     *
     * @return  Query
     */
    public static function convertAllToWheres(Query $query, $wheres): Query
    {
        if ($wheres === null) {
            return $query;
        }

        if (is_callable($wheres)) {
            return $query->where($wheres);
        }

        $wheres = TypeCast::toArray($wheres);

        foreach ($wheres as $key => $where) {
            if (!is_numeric($key)) {
                $query->where($key, '=', $where);
                continue;
            }

            if (is_string($where)) {
                $query->whereRaw($where);
                continue;
            }

            if (is_array($wheres)) {
                return $query->where(...$wheres);
            }

            $query->where($where);
        }

        return $query;
    }

    public function formatDateTime(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format($this->getDateFormat());
    }
}
