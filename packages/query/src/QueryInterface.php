<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

/**
 * Interface QueryInterface
 */
interface QueryInterface
{
    /**
     * Make query object as SQL string.
     *
     * @return  string
     */
    public function __toString();
}
