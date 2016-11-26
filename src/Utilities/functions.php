<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */
declare(strict_types = 1);

if (!function_exists('show')) {
    /**
     * Dump Array or Object as tree node. If send multiple params in this method, this function will batch print it.
     *
     * @param   mixed ...$args Array or Object to dump.
     *
     * @since   2.0
     *
     * @return  void
     */
    // TODO: Use void return type after 7.1 available.
    function show(...$args)
    {
        echo \Windwalker\Utilities\ArrayHelper::show(...$args);
    }
}
