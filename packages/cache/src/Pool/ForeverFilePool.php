<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Cache\Pool;

/**
 * The ForeverFileStorage class.
 *
 * @since  3.2
 */
class ForeverFilePool extends FilePool
{
    /**
     * Check whether or not the cached data by id has expired.
     *
     * @param   string $key The storage entry identifier.
     *
     * @return  boolean  True if the data has expired.
     *
     * @since   3.2
     */
    public function isExpired($key)
    {
        return false;
    }
}
