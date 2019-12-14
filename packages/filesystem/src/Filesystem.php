<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Filesystem;

use Windwalker\Filesystem\Exception\FileNotFoundException;
use Windwalker\Filesystem\Exception\FilesystemException;

/**
 * Class Filesystem
 *
 * @since 2.0
 */
class Filesystem
{
    public const PATH_ABSOLUTE = 1;

    public const PATH_RELATIVE = 2;

    public const PATH_BASENAME = 4;

    /**
     * Create a folder -- and all necessary parent folders.
     *
     * @param   string  $path A path to create from the base path.
     * @param   integer $mode Directory permissions to set for folders created. 0755 by default.
     *
     * @return  boolean  True if successful.
     *
     * @since   2.0
     * @throws  FilesystemException
     */
    public function mkdir(string $path = '', int $mode = 0755): bool
    {
        static $nested = 0;

        // Check to make sure the path valid and clean
        $path = Path::clean($path);

        // Check if parent dir exists
        $parent = dirname($path);

        if (!is_dir($parent)) {
            // Prevent infinite loops!
            $nested++;

            if ($nested > 20 || $parent === $path) {
                throw new FilesystemException(__METHOD__ . ': Infinite loop detected');
            }

            // Create the parent directory
            if ($this->mkdir($parent, $mode) !== true) {
                // Folder::create throws an error
                $nested--;

                return false;
            }

            // OK, parent directory has been created
            $nested--;
        }

        // Check if dir already exists
        if (is_dir($path)) {
            return true;
        }

        // First set umask
        $origmask = @umask(0);

        try {
            if (@!mkdir($path, $mode) && !is_dir($path)) {
                throw new FilesystemException(error_get_last()['message']);
            }
        } finally {
            @umask($origmask);
        }

        return true;
    }

    /**
     * copy
     *
     * @param string $src
     * @param string $dest
     * @param bool   $force
     *
     * @return  bool
     */
    public function copy($src, $dest, $force = false): bool
    {
        $result = null;

        if (is_dir($src)) {
            $result = $this->copyFolder($src, $dest, $force);
        } elseif (is_file($src)) {
            $result = $this->copyFile($src, $dest, $force);
        }

        return $result;
    }

    /**
     * copyFolder
     *
     * @param  string  $src
     * @param  string  $dest
     * @param  bool    $force
     *
     * @return  bool
     */
    private function copyFolder(string $src, string $dest, bool $force = false): bool
    {
        // Eliminate trailing directory separators, if any
        $src = rtrim($src, '/\\');
        $dest = rtrim($dest, '/\\');

        if (!is_dir($src)) {
            throw new FileNotFoundException(sprintf(
                'Source folder not found: %s',
                $src
            ));
        }

        if (is_dir($dest) && !$force) {
            throw new FileNotFoundException(sprintf(
                'Destination folder exists: %s',
                $dest
            ));
        }

        // Make sure the destination exists
        if (!$this->mkdir($dest)) {
            throw new FilesystemException(sprintf(
                'Cannot create destination folder: %s',
                $dest
            ));
        }

        $sources = $this->items($src, true);

        // Walk through the directory copying files and recursing into folders.
        /** @var FileObject $file */
        foreach ($sources as $file) {
            $rFile = $file->getRelativePathFrom($src);

            $srcFile = $src . '/' . $rFile;
            $destFile = $dest . '/' . $rFile;

            if (is_dir($srcFile)) {
                $this->mkdir($destFile);
            } elseif (is_file($srcFile)) {
                $this->copyFile($srcFile, $destFile, $force);
            }
        }

        return true;
    }

    /**
     * Copies a file
     *
     * @param   string $src   The path to the source file
     * @param   string $dest  The path to the destination file
     * @param   bool   $force Force copy.
     *
     * @throws \UnexpectedValueException
     * @throws Exception\FilesystemException
     * @return  boolean  True on success
     *
     * @since   2.0
     */
    private function copyFile(string $src, string $dest, bool $force = false): bool
    {
        // Check src path
        if (!is_readable($src)) {
            throw new \UnexpectedValueException(__METHOD__ . ': Cannot find or read file: ' . $src);
        }

        // Check folder exists
        $dir = dirname($dest);

        if (!is_dir($dir)) {
            $this->mkdir($dir);
        }

        // Check is a folder or file
        if (file_exists($dest)) {
            if ($force) {
                $this->delete($dest);
            } else {
                throw new FilesystemException($dest . ' has exists, copy failed.');
            }
        }

        return copy($src, $dest);
    }

    /**
     * move
     *
     * @param string $src
     * @param string $dest
     * @param bool   $force
     *
     * @return  bool
     */
    public function move($src, $dest, $force = false)
    {
        // Check src path
        if (!is_readable($src)) {
            return 'Cannot find source file.';
        }

        // Delete first if exists
        if (file_exists($dest)) {
            if ($force) {
                $this->delete($dest);
            } else {
                throw new FilesystemException('File: ' . $dest . ' exists, move failed.');
            }
        }

        $dir = dirname($dest);

        if (!is_dir($dir)) {
            $this->mkdir($dir);
        }

        if (!@rename($src, $dest)) {
            throw new FilesystemException(
                error_get_last()['message']
            );
        }

        return true;
    }

    /**
     * delete
     *
     * @param string $path
     *
     * @return  bool
     */
    public function delete($path): bool
    {
        $path = Path::clean(FileObject::unwrap($path));

        if (is_dir($path)) {
            // Delete children files
            $files = $this->files($path, true);

            /** @var FileObject $file */
            foreach ($files as $file) {
                $this->delete($file);
            }

            // Delete children folders
            $folders = $this->folders($path, true);

            /** @var FileObject $folder */
            foreach ($folders as $folder) {
                $this->delete($folder);
            }
        }

        // Try making the file writable first. If it's read-only, it can't be deleted
        // on Windows, even if the parent folder is writable
        @chmod($path, 0777);

        // In case of restricted permissions we zap it one way or the other
        // as long as the owner is either the webserver or the ftp
        if (is_dir($path)) {
            $result = @rmdir($path);
        } else {
            $result = @unlink($path);
        }

        if (!$result) {
            new FilesystemException(error_get_last()['message']);
        }

        return $result;
    }

    /**
     * files
     *
     * @param   string $path
     * @param   bool   $recursive
     *
     * @return  \CallbackFilterIterator|FileObject[]
     */
    public function files($path, $recursive = false): \Traversable
    {
        /**
         * Files callback
         *
         * @param \SplFileInfo                $current  Current item's value
         * @param string                      $key      Current item's key
         * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
         *
         * @return boolean   TRUE to accept the current item, FALSE otherwise
         */
        $callback = static function ($current, $key, $iterator) {
            return $current->isFile();
        };

        return $this->findByCallback($path, $callback, $recursive);
    }

    /**
     * folders
     *
     * @param   string  $path
     * @param   bool    $recursive
     *
     * @return  \CallbackFilterIterator|FileObject[]
     */
    public function folders(string $path, bool $recursive = false): \Traversable
    {
        /**
         * Files callback
         *
         * @param \SplFileInfo                $current  Current item's value
         * @param string                      $key      Current item's key
         * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
         *
         * @return boolean   TRUE to accept the current item, FALSE otherwise
         */
        $callback = static function ($current, $key, $iterator) use ($path, $recursive) {
            if ($recursive) {
                // Ignore self
                if ($iterator->getRealPath() === Path::clean($path)) {
                    return false;
                }

                // If set to recursive, every returned folder name will include a dot (.),
                // so we can't using isDot() to detect folder.
                return $iterator->isDir() && ($iterator->getBasename() !== '..');
            }

            return $iterator->isDir() && !$iterator->isDot();
        };

        return $this->findByCallback($path, $callback, $recursive);
    }

    /**
     * items
     *
     * @param   string  $path
     * @param   bool    $recursive
     *
     * @return  \CallbackFilterIterator|FileObject[]
     */
    public function items($path, $recursive = false): \Traversable
    {
        /**
         * Files callback
         *
         * @param \SplFileInfo                $current  Current item's value
         * @param string                      $key      Current item's key
         * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
         *
         * @return boolean   TRUE to accept the current item, FALSE otherwise
         */
        $callback = static function ($current, $key, $iterator) use ($path, $recursive) {
            if ($recursive) {
                // Ignore self
                $cPath = $current->isDir() ? $current->getPath() : $current->getPathname();

                if ($cPath === Path::clean($path)) {
                    return false;
                }

                // If set to recursive, every returned folder name will include a dot (.),
                // so we can't using isDot() to detect folder.
                return ($current->getBasename() !== '..');
            }

            return !$current->isDot();
        };

        return $this->findByCallback($path, $callback, $recursive);
    }

    /**
     * Find one file and return.
     *
     * @param  string  $path          The directory path.
     * @param  mixed   $condition     Finding condition, that can be a string, a regex or a callback function.
     *                                Callback example:
     *                                <code>
     *                                function($current, $key, $iterator)
     *                                {
     *                                return @preg_match('^Foo', $current->getFilename())  && ! $iterator->isDot();
     *                                }
     *                                </code>
     * @param  boolean $recursive     True to resursive.
     *
     * @return  \SplFileInfo  Finded file info object.
     *
     * @since  2.0
     */
    public function findOne($path, $condition, $recursive = false)
    {
        $iterator = new \LimitIterator($this->find($path, $condition, $recursive), 0, 1);

        $iterator->rewind();

        return $iterator->current();
    }

    /**
     * Support node style double star finder.
     *
     * @param string $pattern
     * @param int    $flags
     *
     * @return  array
     *
     * @since  3.5
     */
    public static function glob(string $pattern, int $flags = 0): array
    {
        $pattern = Path::clean($pattern);

        if (strpos($pattern, '**') === false) {
            $files = glob($pattern, $flags);
        } else {
            $position = strpos($pattern, '**');
            $rootPattern = substr($pattern, 0, $position - 1);
            $restPattern = substr($pattern, $position + 2);
            $patterns = [$rootPattern . $restPattern];
            $rootPattern .= DIRECTORY_SEPARATOR . '*';

            while ($dirs = glob($rootPattern, GLOB_ONLYDIR)) {
                $rootPattern .= DIRECTORY_SEPARATOR . '*';

                foreach ($dirs as $dir) {
                    $patterns[] = $dir . $restPattern;
                }
            }

            $files = [];

            foreach ($patterns as $pat) {
                $files[] = static::glob($pat, $flags);
            }

            $files = array_merge(...$files);
        }

        $files = array_unique($files);

        sort($files);

        return $files;
    }

    /**
     * globAll
     *
     * @param string $baseDir
     * @param array  $patterns
     * @param int    $flags
     *
     * @return  array
     *
     * @since  3.5
     */
    public static function globAll(string $baseDir, array $patterns, int $flags = 0): array
    {
        $files = [];
        $inverse = [];

        foreach ($patterns as $pattern) {
            if (strpos($pattern, '!') === 0) {
                $pattern = substr($pattern, 1);

                $inverse[] = static::glob(
                    rtrim($baseDir, '\\/') . '/' . ltrim($pattern, '\\/'),
                    $flags
                );
            } else {
                $files[] = static::glob(
                    rtrim($baseDir, '\\/') . '/' . ltrim($pattern, '\\/'),
                    $flags
                );
            }
        }

        if ($files !== []) {
            $files = array_unique(array_merge(...$files));
        }

        if ($inverse !== []) {
            $inverse = array_unique(array_merge(...$inverse));
        }

        return array_diff($files, $inverse);
    }

    /**
     * Find all files which matches condition.
     *
     * @param  string  $path        The directory path.
     * @param  mixed   $condition   Finding condition, that can be a string, a regex or a callback function.
     *                              Callback example:
     *                              <code>
     *                              function($current, $key, $iterator)
     *                              {
     *                              return @preg_match('^Foo', $current->getFilename())  && ! $iterator->isDot();
     *                              }
     *                              </code>
     * @param  boolean $recursive   True to resursive.
     * @param  boolean $toArray     True to convert iterator to array.
     *
     * @return  \CallbackFilterIterator|FileObject[]  Found files or paths iterator.
     *
     * @since  2.0
     */
    public function find($path, $condition, $recursive = false, $toArray = false)
    {
        // If conditions is string or array, we make it to regex.
        if (!($condition instanceof \Closure) && !($condition instanceof FileComparatorInterface)) {
            if (is_array($condition)) {
                $condition = '/(' . implode('|', $condition) . ')/';
            } else {
                $condition = '/' . (string) $condition . '/';
            }

            /**
             * Files callback
             *
             * @param \SplFileInfo                $current  Current item's value
             * @param string                      $key      Current item's key
             * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
             *
             * @return boolean   TRUE to accept the current item, FALSE otherwise
             */
            $condition = function ($current, $key, $iterator) use ($condition) {
                return @preg_match($condition, $iterator->getFilename()) && !$iterator->isDot();
            };
        } elseif ($condition instanceof FileComparatorInterface) {
            // If condition is compare object, wrap it with callback.
            /**
             * Files callback
             *
             * @param \SplFileInfo                $current  Current item's value
             * @param string                      $key      Current item's key
             * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
             *
             * @return boolean   TRUE to accept the current item, FALSE otherwise
             */
            $condition = function ($current, $key, $iterator) use ($condition) {
                return $condition->compare($current, $key, $iterator);
            };
        }

        return $this->findByCallback($path, $condition, $recursive, $toArray);
    }

    /**
     * Using a closure function to filter file.
     *
     * Reference: http://www.php.net/manual/en/class.callbackfilteriterator.php
     *
     * @param  string   $path      The directory path.
     * @param  \Closure $callback  A callback function to filter file.
     * @param  boolean  $recursive True to recursive.
     *
     * @return  \CallbackFilterIterator|FileObject[]  Filtered file or path iteator.
     *
     * @since  2.0
     */
    public function findByCallback(string $path, \Closure $callback, $recursive = false): \CallbackFilterIterator
    {
        return new \CallbackFilterIterator($this->createIterator($path, $recursive), $callback);
    }

    /**
     * Create file iterator of current dir.
     *
     * @param  string  $path      The directory path.
     * @param  boolean $recursive True to recursive.
     * @param  integer $options   FilesystemIterator Flags provides which will affect the behavior of some methods.
     *
     * @return  \Traversable|FileObject[]  File & dir iterator.
     */
    public function createIterator(string $path, bool $recursive = false, int $options = null): \Traversable
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
     * iteratorToArray
     *
     * @param \Traversable $iterator
     *
     * @return  array
     */
    public static function toArray(\Traversable $iterator): array
    {
        $array = [];

        foreach ($iterator as $key => $file) {
            $array[] = FileObject::unwrap($file);
        }

        return $array;
    }

    /**
     * throwException
     *
     * @param  string           $message
     * @param  string|null      $path
     * @param  \Throwable|null  $previous
     *
     * @return  void
     */
    public function throwException(string $message, string $path = '', ?\Throwable $previous = null): void
    {
        throw new FilesystemException($message, 0, $previous, $path);
    }
}
