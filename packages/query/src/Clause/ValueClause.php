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
     * @var string|mixed|Query
     */
    protected $value;

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * AsClause constructor.
     *
     * @param  string|Query|RawWrapper  $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        $value = $this->value;

        if ($value instanceof RawWrapper) {
            return (string) $value();
        }

        if ($value instanceof Query) {
            return '(' . $value . ')';
        }

        return $this->getPlaceholder();
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder ? ':' . ltrim($this->placeholder, ':') : '?';
    }

    /**
     * Method to get property Column
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function &getValue()
    {
        return $this->value;
    }

    /**
     * Method to set property column
     *
     * @param  mixed  $value
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Method to set property placeholder
     *
     * @param  string  $placeholder
     *
     * @return  static  Return self to support chaining.
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }
}
