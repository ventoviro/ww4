<?php declare(strict_types=1);

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Utilities\Compare;

use Windwalker\Utilities\TypeCast;

/**
 * The CompareHelper class.
 *
 * @since  2.0
 */
class CompareHelper
{
    /**
     * Compare two values.
     *
     * @param mixed  $compare1 The compare1 value.
     * @param string $operator The compare operator.
     * @param mixed  $compare2 The compare2 calue.
     * @param bool   $strict   Use strict compare.
     *
     * @return  boolean
     *
     * @throws \InvalidArgumentException
     */
    public static function compare($compare1, string $operator, $compare2, $strict = false): bool
    {
        $operator = strtolower(trim($operator));

        switch ($operator) {
            case '=':
            case '==':
            case 'eq':
                return $strict ? $compare1 === $compare2 : $compare1 == $compare2;
                break;

            case '===':
                return $compare1 === $compare2;
                break;

            case '!=':
            case 'neq':
                return $strict ? $compare1 !== $compare2 : $compare1 != $compare2;
                break;

            case '!==':
                return $compare1 !== $compare2;
                break;

            case '>':
            case 'gt':
                return $compare1 > $compare2;
                break;

            case '>=':
            case 'gte':
                return $compare1 >= $compare2;
                break;

            case '<':
            case 'lt':
                return $compare1 < $compare2;
                break;

            case '<=':
            case 'lte':
                return $compare1 <= $compare2;
                break;

            case 'in':
                return in_array($compare1, TypeCast::toArray($compare2), $strict);
                break;

            case 'not in':
            case 'not-in':
            case 'notin':
            case 'nin':
                return !in_array($compare1, TypeCast::toArray($compare2), $strict);
                break;

            default:
                throw new \InvalidArgumentException('Invalid compare operator: ' . $operator);
        }
    }
}
