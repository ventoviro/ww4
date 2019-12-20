<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Filesystem\Iterator;

use Windwalker\Filesystem\Exception\FileNotFoundException;
use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Filesystem;
use Windwalker\Filesystem\Path;
use Windwalker\Utilities\Iterator\NestedIterator;

/**
 * The FilesIterator class.
 */
class FilesIterator extends NestedIterator
{
    /**
     * create
     *
     * @param  string    $path
     * @param  bool      $recursive
     * @param  int|null  $options
     *
     * @return  static
     */
    public static function create(string $path, bool $recursive = false, int $options = null)
    {
        $instance = new static(static::createInnerIterator($path, $recursive, $options));

        return $instance->filter(static function (\SplFileInfo $file) use ($recursive, $path) {
            if ($file->getBasename() === '..') {
                return false;
            }

            if ($recursive) {
                // show($file->getRealPath(), Path::normalize($path));
                if ($file->getRealPath() === Path::normalize($path)) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Create file iterator of current dir.
     *
     * @param  string   $path       The directory path.
     * @param  boolean  $recursive  True to recursive.
     * @param  integer  $options    FilesystemIterator Flags provides which will affect the behavior of some methods.
     *
     * @return  \Iterator  File & dir iterator.
     */
    public static function createInnerIterator(string $path, bool $recursive = false, int $options = null): \Iterator
    {
        $path = Path::clean($path);

        if ($recursive) {
            $options = $options ?: (\FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO);
        } else {
            $options = $options ?: (\FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO
                | \FilesystemIterator::SKIP_DOTS);
        }

        try {
            $iterator = new \RecursiveDirectoryIterator($path, $options);
        } catch (\UnexpectedValueException $e) {
            throw new FileNotFoundException(
                sprintf('Failed to open dir: %s', $path),
                $e->getCode(),
                $e
            );
        }

        $iterator->setInfoClass(FileObject::class);

        // If rescurive set to true, use RecursiveIteratorIterator
        return $recursive ? new \RecursiveIteratorIterator($iterator) : $iterator;
    }

    /**
     * toArray
     *
     * @return  array
     */
    public function toArray(): array
    {
        return Filesystem::toArray($this);
    }
}
