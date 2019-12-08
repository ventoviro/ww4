<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later.
 */

declare(strict_types=1);

namespace Windwalker\Cache\Pool;

/**
 * The PhpFileStorage class.
 *
 * @since  3.0
 */
class PhpFilePool extends FilePool
{
    /**
     * read
     *
     * @param   string $filename
     *
     * @return  string
     */
    protected function read($filename)
    {
        return include $filename;
    }

    /**
     * write
     *
     * @param string $filename
     * @param string $value
     * @param int    $options
     *
     * @return  boolean
     */
    protected function write($filename, $value, $options)
    {
        return parent::write($filename, $value, $options);
    }
}
