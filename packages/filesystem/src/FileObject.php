<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Filesystem;

/**
 * The FileObject class.
 */
class FileObject extends \SplFileInfo
{
    /**
     * unwrap
     *
     * @param string|\SplFileInfo $file
     *
     * @return  string
     */
    public static function unwrap($file): string
    {
        if ($file instanceof \SplFileInfo) {
            if ($file->isDir()) {
                return $file->getPath();
            }

            return $file->getPathname();
        }

        return (string) $file;
    }

    /**
     * getRelativePathFrom
     *
     * @param string|\SplFileInfo $src
     *
     * @return  string
     */
    public function getRelativePathFrom($src): string
    {
        $src = Path::normalize(static::unwrap($src));

        $path = $this->getPathname();

        if ($path === $src) {
            return $path;
        }

        if (strpos($path, $src) !== 0) {
            return $path;
        }

        return ltrim(substr($path, strlen($src)), DIRECTORY_SEPARATOR);
    }

    /**
     * getPathname
     *
     * @return  string
     */
    public function getPathname(): string
    {
        return rtrim(parent::getPathname(), '.');
    }
}
