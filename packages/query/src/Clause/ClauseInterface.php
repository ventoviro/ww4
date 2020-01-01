<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Clause;

/**
 * Interface ClauseInterface
 */
interface ClauseInterface
{
    /**
     * Magic function to convert the query element to a string.
     *
     * @return  string
     *
     * @since   2.0
     */
    public function __toString(): string;
}
