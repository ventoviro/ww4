<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Filesystem;

use Windwalker\Filesystem\Iterator\FilesIterator;
use Windwalker\Utilities\Iterator\UniqueIterator;

/**
 * A PathLocator collection class
 *
 * @since  2.0
 */
class PathCollection
{
    /**
     * Paths bag.
     *
     * @var FileObject[]
     */
    protected $paths = [];

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * PathCollection constructor.
     *
     * @param  array  $paths  The PathLocator array.
     *
     * @since  2.0
     */
    public function __construct(array $paths = [])
    {
        $this->addPaths($paths);
    }

    /**
     * Batch add paths to bag.
     *
     * @param  array  $paths  Paths to add to path bag, string will be converted to PathLocator object.
     *
     * @return  static  Return this object to support chaining.
     *
     * @since  2.0
     */
    public function addPaths(array $paths)
    {
        $new = clone $this;

        foreach ($paths as $path) {
            $new = $new->add($path);
        }

        return $new;
    }

    /**
     * Add one path to bag.
     *
     * @param  string|\SplFileInfo  $path  The path your want to store in bag,
     *                                     have to be a string or FileObject.
     *
     * @return  static  Return new object to support chaining.
     *
     * @throws \InvalidArgumentException
     * @since  2.0
     */
    public function add($path)
    {
        $new = clone $this;

        $new->paths[] = FileObject::wrap($path);

        return $new;
    }

    /**
     * Get all paths with key from bag.
     *
     * @return  array  An array includes all path objects.
     *
     * @since  2.0
     */
    public function &getPaths()
    {
        return $this->paths;
    }

    /**
     * Using key to get a path.
     *
     * @param  int  $key  The key of path you want to get.
     *
     * @return  FileObject
     *
     * @since  2.0
     */
    public function getPath(int $key): FileObject
    {
        return $this->paths[$key] ?? null;
    }

    /**
     * Method to set property paths
     *
     * @param  FileObject[]  $paths
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function withPaths(array $paths)
    {
        $new = clone $this;

        $new->paths = $paths;

        return $new;
    }

    /**
     * Append all paths' iterator into an OuterIterator.
     *
     * @param  \Closure  $getter  Contains the logic that how to get iterator from file object.
     *
     * @return  FilesIterator  Appended iterators.
     *
     * @since  2.0
     */
    private function createIterator(\Closure $getter = null): FilesIterator
    {
        $iter = new \AppendIterator();

        foreach ($this->paths as $path) {
            if ($this->isChild($path)) {
                continue;
            }

            $iter->append($getter($path));
        }

        return new FilesIterator(new UniqueIterator($iter));
    }

    /**
     * Get all files and folders as an iterator.
     *
     * @param  boolean  $recursive  True to support recrusive.
     *
     * @return  FilesIterator  An OutterIterator contains all paths' iterator.
     *
     * @since  2.0
     */
    public function items($recursive = false): FilesIterator
    {
        return $this->createIterator(
            static function (FileObject $path) use ($recursive) {
                return $path->items($recursive);
            }
        );
    }

    /**
     * Get file iterator of all paths
     *
     * @param  boolean  $recursive  True to resursive.
     *
     * @return  FilesIterator  Iterator only include files.
     */
    public function files($recursive = false): FilesIterator
    {
        return $this->createIterator(
            static function (FileObject $path) use ($recursive) {
                return $path->files($recursive);
            }
        );
    }

    /**
     * Get folder iterator of all paths
     *
     * @param  boolean  $recursive  True to resursive.
     *
     * @return  FilesIterator  Iterator only include dirs.
     */
    public function folders($recursive = false): FilesIterator
    {
        return $this->createIterator(
            static function (FileObject $path) use ($recursive) {
                return $path->folders($recursive);
            }
        );
    }

    /**
     * Set prefix to all paths.
     *
     * @param  string  $prefix  The prefix path you want to prepend when path convert to string.
     *
     * @return  static  Return new object.
     */
    public function withPrefix(string $prefix)
    {
        $new = clone $this;

        $new->prefix = $prefix;

        return $new;
    }

    /**
     * Append a new path to all paths.
     *
     * @param  string  $appended  Path to append.
     *
     * @return  static  Return new object.
     */
    public function appendAll(string $appended)
    {
        return $this->map(static function (FileObject $path) use ($appended) {
            return $path->appendPath($appended);
        });
    }

    /**
     * Prepend a new path to all paths.
     *
     * @param  string  $prepended  Path to prepend.
     *
     * @return  static  Return new object.
     *
     * @since  2.0
     */
    public function prependAll(string $prepended)
    {
        return $this->map(static function (FileObject $path) use ($prepended) {
            return $path->prependPath($prepended);
        });
    }

    /**
     * Map all elements.
     *
     * @param  callable  $callback
     *
     * @return  static Return new object.
     */
    public function map(callable $callback)
    {
        $new = clone $this;

        $new->paths = array_map($callback, $new->paths);

        return $new;
    }

    /**
     * Convert paths bag to array, and every path to string.
     *
     * @return  array  Raw paths.
     *
     * @since  2.0
     */
    public function toArray(): array
    {
        $paths = [];

        foreach ($this->paths as $path) {
            $paths[] = $path->getPathname();
        }

        return $paths;
    }

    /**
     * Is this path a subdir of another path in bag?
     *
     * When running recursive scan dir, we have to avoid to re scan same dir.
     *
     * @param  string|\SplFileInfo  $path  The path to detect is subdir or not.
     *
     * @return  boolean  Is subdir or not.
     *
     * @since  2.0
     */
    public function isChild($path): bool
    {
        foreach ($this->paths as $member) {
            if ($member->isChildOf($path)) {
                return true;
            }
        }

        return false;
    }
}
