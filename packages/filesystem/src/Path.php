<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Filesystem;

use Windwalker\Filesystem\Path\PathCollection;

/**
 * A Path handling class
 *
 * @since  2.0
 */
class Path
{
    /**
     * Chmods files and directories recursively to given permissions.
     *
     * @param   string $path       Root path to begin changing mode [without trailing slash].
     * @param   string $filemode   Octal representation of the value to change file mode to [null = no change].
     * @param   string $foldermode Octal representation of the value to change folder mode to [null = no change].
     *
     * @return  boolean  True if successful [one fail means the whole operation failed].
     *
     * @since   2.0
     */
    public static function setPermissions($path, $filemode = '0644', $foldermode = '0755')
    {
        // Initialise return value
        $ret = true;

        if (is_dir($path)) {
            $dh = opendir($path);

            while ($file = readdir($dh)) {
                if ($file !== '.' && $file !== '..') {
                    $fullpath = $path . '/' . $file;

                    if (is_dir($fullpath)) {
                        if (!self::setPermissions($fullpath, $filemode, $foldermode)) {
                            $ret = false;
                        }
                    } else {
                        if (isset($filemode)) {
                            if (!@ chmod($fullpath, octdec($filemode))) {
                                $ret = false;
                            }
                        }
                    }
                }
            }

            closedir($dh);

            if (isset($foldermode)) {
                if (!@ chmod($path, octdec($foldermode))) {
                    $ret = false;
                }
            }
        } else {
            if (isset($filemode)) {
                $ret = @ chmod($path, octdec($filemode));
            }
        }

        return $ret;
    }

    /**
     * Get the permissions of the file/folder at a give path.
     *
     * @param   string  $path     The path of a file/folder.
     * @param   boolean $toString Convert permission number to string.
     *
     * @return  string  Filesystem permissions.
     *
     * @since   2.0
     */
    public static function getPermissions($path, $toString = false)
    {
        $path = self::clean($path);
        $mode = @ decoct(@ fileperms($path) & 0777);

        if (!$toString) {
            return $mode;
        }

        if (strlen($mode) < 3) {
            return '---------';
        }

        $parsedMode = '';

        for ($i = 0; $i < 3; $i++) {
            // Read
            $parsedMode .= ($mode[$i] & 04) ? "r" : "-";

            // Write
            $parsedMode .= ($mode[$i] & 02) ? "w" : "-";

            // Execute
            $parsedMode .= ($mode[$i] & 01) ? "x" : "-";
        }

        return $parsedMode;
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
    public static function clean(string $path, string $ds = DIRECTORY_SEPARATOR)
    {
        if ($path === '') {
            return $path;
        }

        $prefix = '';

        if (strpos($path, '://') !== false) {
            $extracted = explode('://', $path, 2);

            if (count($extracted) === 1) {
                return $extracted[0];
            }

            $prefix = $extracted[0] . '://';
            $path = $extracted[1];
        } elseif (preg_match('/(\w+):[\/\\\\](.*)/', $path, $matches)) {
            if ($matches[2] === '') {
                return $path;
            }

            $prefix = $matches[1] . ':\\';
            $path = $matches[2];
        }

        $path = trim($path);

        if (($ds === '\\') && ($path[0] === '\\') && ($path[1] === '\\')) {
            // Remove double slashes and backslashes and convert all slashes and backslashes to DIRECTORY_SEPARATOR
            // If dealing with a UNC path don't forget to prepend the path with a backslash.
            $path = "\\" . preg_replace('#[/\\\\]+#', $ds, $path);
        } else {
            $path = preg_replace('#[/\\\\]+#', $ds, $path);
        }

        return $prefix . $path;
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
    public static function normalize($path, $ds = DIRECTORY_SEPARATOR)
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

        return rtrim(implode($ds, $parts), '.' . DIRECTORY_SEPARATOR);
    }

    /**
     * Check file exists and also the filename cases.
     *
     * @param string $path      The file path to check.
     * @param bool   $sensitive Sensitive file name case.
     *
     * @return  bool
     * @throws \UnexpectedValueException
     */
    public static function exists(string $path, bool $sensitive = false): bool
    {
        if ($sensitive === null) {
            return file_exists($path);
        }

        $path = static::normalize($path, DIRECTORY_SEPARATOR);

        if (!$sensitive) {
            $lowerfile = strtolower($path);

            foreach (glob(dirname($path) . DIRECTORY_SEPARATOR . '*') as $file) {
                if (strtolower($file) === $lowerfile) {
                    return true;
                }
            }
        } else {
            foreach (glob(dirname($path) . DIRECTORY_SEPARATOR . '*') as $file) {
                if ($file === $path) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Fix a path with correct file name cases.
     *
     * @param string $path
     *
     * @return  string
     */
    public static function fixCase(string $path): string
    {
        $path = static::normalize($path, DIRECTORY_SEPARATOR);

        $lowerfile = strtolower($path);

        foreach (glob(dirname($path) . DIRECTORY_SEPARATOR . '*') as $file) {
            if (strtolower($file) === $lowerfile) {
                return $file;
            }
        }

        return $path;
    }
}
