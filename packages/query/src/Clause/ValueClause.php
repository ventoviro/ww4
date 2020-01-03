<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Clause;

use Windwalker\Query\Query;
use Windwalker\Utilities\Wrapper\RawWrapper;

/**
 * The ValueCaluse class.
 */
class ValueClause implements ClauseInterface
{
    /**
     * @var string|Query
     */
    protected $value;

    /**
     * AsClause constructor.
     *
     * @param  string|Query|RawWrapper  $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        $column = $this->value;

        if ($column instanceof Query) {
            $column = '(' . $column . ')';
        }

        return (string) $column;
    }

    /**
     * Method to get property Column
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Method to set property column
     *
     * @param  string  $column
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function value($column)
    {
        $this->value = $column;

        return $this;
    }
}
