<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker;

use FilesystemIterator;
use Webmozart\Glob\Glob;
use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Filesystem;

/**
 * Support node style double star finder.
 *
 * ```
 * \Windwalker\glob('/var/www/foo/**\/*.php')
 * ```
 *
 * @param  string  $pattern
 * @param  int     $flags
 *
 * @return  array
 */
function glob(string $pattern, int $flags = 0): array
{
    if (!class_exists(Glob::class)) {
        throw new \DomainException('Please install webmozart/glob first');
    }

    return Glob::glob($pattern, $flags);
}

/**
 * glob_all
 *
 * @param  array  $patterns
 * @param  int    $flags
 *
 * @return  array
 */
function glob_all(
    array $patterns,
    int $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO
): array {
    return Filesystem::globAll($patterns, $flags)->toArray();
}

/**
 * Create a file object from file or dir path.
 *
 * @param  string|\SplFileInfo  $path
 * @param  string|null          $root
 *
 * @return  FileObject
 */
function fs($path, ?string $root = null): FileObject
{
    return FileObject::wrap($path, $root);
}
