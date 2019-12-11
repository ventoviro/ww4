<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Storage;

/**
 * The PhpFileStorage class.
 */
class PhpFileStorage extends FileStorage
{
    /**
     * read
     *
     * @param   string $filename
     *
     * @return  string
     */
    protected function read(string $filename): string
    {
        return include $filename;
    }
}
