<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Filesystem\Path;

/**
 * A Path locator class
 *
 * @since  2.0
 */
class PathLocator implements PathLocatorInterface, \IteratorAggregate
{
    /**
     * Path prefix
     *
     * @var string
     *
     * @since  2.0
     */
    protected $prefix = '';

    /**
     * A variable to store paths
     *
     * @var string
     *
     * @since  2.0
     */
    protected $path = '';

    /**
     * Constructor to handle path.
     *
     * @param   string $path Path to parse.
     *
     * @since   2.0
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Replace with a new path.
     *
     * @param   string $path Path to parse.
     *
     * @return  static  Return this object to support chaining.
     *
     * @since  2.0
     */
    public function redirect(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get file iterator of current dir.
     *
     * @return  \RecursiveDirectoryIterator  File & dir iterator.
     */
    public function getIterator(): \Traversable
    {
        // If we put this object into a foreach, return all files and folders to iterator.
        return new \RecursiveDirectoryIterator((string) $this);
    }

    /**
     * getFileInfo
     *
     * @return  \SplFileInfo
     */
    public function getFileInfo(): \SplFileInfo
    {
        return new \SplFileInfo((string) $this);
    }

    /**
     * getFile
     *
     * @return  \SplFileObject
     */
    public function getFile(): \SplFileObject
    {
        return new \SplFileObject((string) $this);
    }

    /**
     * Function to strip additional / or \ in a path name.
     *
     * @param   string $path The path to clean.
     * @param   string $ds   Directory separator (optional).
     *
     * @return  string  The cleaned path.
     *
     * @since   2.0
     * @throws  \UnexpectedValueException If $path is not a string.
     * @throws  \InvalidArgumentException
     */
    public static function clean(string $path, string $ds = DIRECTORY_SEPARATOR): string
    {
        if (!is_string($path)) {
            throw new \UnexpectedValueException(__CLASS__ . '::clean $path is not a string.');
        }

        if ($path === '') {
            throw new \InvalidArgumentException('Path length is 0.');
        }

        $path = trim($path);

        if (($ds === '\\') && ($path[0] === '\\') && ($path[1] === '\\')) {
            // Remove double slashes and backslashes and convert all slashes and backslashes to DIRECTORY_SEPARATOR
            // If dealing with a UNC path don't forget to prepend the path with a backslash.
            $path = "\\" . preg_replace('#[/\\\\]+#', $ds, $path);
        } else {
            $path = (string) preg_replace('#[/\\\\]+#', $ds, $path);
        }

        return $path;
    }

    /**
     * Normalize a path. This method will do clean() first to replace slashes and remove '..' to create a
     * Clean path. Unlike realpath(), if this path not exists, normalise() will still return this path.
     *
     * @param   string $path The path to normalize.
     * @param   string $ds   Directory separator (optional).
     *
     * @return  string  The normalized path.
     *
     * @since   2.0.4
     * @throws  \UnexpectedValueException If $path is not a string.
     */
    public static function normalize(string $path, string $ds = DIRECTORY_SEPARATOR): string
    {
        $parts = [];
        $path = static::clean($path, $ds);
        $segments = explode($ds, $path);

        foreach ($segments as $segment) {
            if ($segment !== '.') {
                $test = array_pop($parts);

                if (null === $test) {
                    $parts[] = $segment;
                } elseif ($segment === '..') {
                    if ($test === '..') {
                        $parts[] = $test;
                    }

                    if ($test === '..' || $test === '') {
                        $parts[] = $segment;
                    }
                } else {
                    $parts[] = $test;
                    $parts[] = $segment;
                }
            }
        }

        return implode($ds, $parts);
    }

    /**
     * Detect is current path a dir?
     *
     * @return  boolean  True if is a dir.
     *
     * @since  2.0
     */
    public function isDir(): bool
    {
        return is_dir((string) $this);
    }

    /**
     * Detect is current path a file?
     *
     * @return  boolean  True if is a file.
     *
     * @since  2.0
     */
    public function isFile(): bool
    {
        return is_file((string) $this);
    }

    /**
     * Detect is current path exists?
     *
     * @return  boolean  True if exists.
     *
     * @since  2.0
     */
    public function exists(): bool
    {
        return file_exists((string) $this);
    }

    /**
     * Set a prefix, when this object convert to string,
     * prefix will auto add to the front of path.
     *
     * @param   string $prefix Prefix string to set.
     *
     * @return  static  Return this object to support chaining.
     *
     * @since  2.0
     */
    public function withPrefix(string $prefix = '')
    {
        $new = clone $this;

        $new->prefix = $prefix;

        return $new;
    }

    /**
     * Get a parent path of given condition.
     *
     * @param   bool|int|string $condition Parent condition.
     *
     * @return  static  Return this object to support chaining.
     *
     * @since  2.0
     */
    public function parent($condition = null)
    {
        $new = clone $this;

        $segments = explode(DIRECTORY_SEPARATOR, static::normalize($new->path));

        // Up one level
        if (null === $condition) {
            array_pop($segments);
        } elseif (is_int($condition)) {
            // Up mutiple level
            $new->path = array_slice($segments, 0, -$condition);
        } elseif (is_string($condition)) {
            // Find a dir name and go to this level
            $paths = array_reverse($segments);

            // Find parent
            $n = 0;

            foreach ($paths as $key => $name) {
                if ($key === 0) {
                    // Ignore latest dir
                    continue;
                }

                // Is this dir match condition?
                if ($name === $condition) {
                    $n = $key;
                    break;
                }
            }

            $segments = array_slice($segments, 0, -$n);
        }

        $new->path = implode(DIRECTORY_SEPARATOR, $segments);

        return $new;
    }

    /**
     * Append a new path before current path.
     *
     * @param   string $path Path to append.
     *
     * @return  static  Return this object to support chaining.
     *
     * @since  2.0
     */
    public function append(string $path)
    {
        $new = clone $this;

        $new->path .= DIRECTORY_SEPARATOR . $path;

        return $new;
    }

    /**
     * Append a new path before current path.
     *
     * @param   string $path Path to append.
     *
     * @return  static  Return this object to support chaining.
     *
     * @since  2.0
     */
    public function prepend(string $path)
    {
        $new = clone $this;

        $new->path = $path . DIRECTORY_SEPARATOR . $new->path;

        return $new;
    }

    /**
     * Is this path subdir of given path?
     *
     * @param  string $parent Given path to detect.
     *
     * @return  boolean  Is subdir or not.
     *
     * @since  2.0
     */
    public function isSubdirOf($parent): bool
    {
        $self = (string) $this;

        $parent = static::normalize($parent);

        // Path is self
        if ($self === $parent) {
            return false;
        }

        // Path is parent
        if (strpos($self, $parent) === 0) {
            return true;
        }

        return false;
    }

    /**
     * Convert this object to string.
     *
     * @return  string  Path name.
     *
     * @since  2.0
     */
    public function __toString()
    {
        return static::normalize(
            $this->prefix . DIRECTORY_SEPARATOR . $this->path
        );
    }

    /**
     * Method to get property Path
     *
     * @param  bool  $normalized
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public function get(bool $normalized = true): string
    {
        if ($normalized) {
            return (string) $this;
        }

        return $this->path;
    }
}
