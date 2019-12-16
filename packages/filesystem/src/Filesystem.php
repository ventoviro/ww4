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
use Windwalker\Filesystem\Iterator\FilesIterator;
use Windwalker\Promise\Promise;
use Windwalker\Validator\ValidatorInterface;

/**
 * Class Filesystem
 *
 * @method Promise mkdirAsync(string $path = '', int $mode = 0755)
 * @method Promise copyAsync(string $src, string $dest, bool $force = false)
 * @method Promise moveAsync(string $src, string $dest, bool $force = false)
 * @method Promise deleteAsync(string $path)
 * @method Promise filesAsync(string $path, bool $recursive = false)
 * @method Promise foldersAsync(string $path, bool $recursive = false)
 * @method Promise itemsAsync(string $path, bool $recursive = false)
 * @method Promise findOneAsync(string $path, $condition, bool $recursive = false)
 * @method Promise findAsync(string $path, $condition, bool $recursive = false)
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
    public function copy(string $src, string $dest, bool $force = false): bool
    {
        $result = null;

        if (is_dir($src)) {
            $result = $this->copyFolder($src, $dest, $force);
        } elseif (is_file($src)) {
            $result = $this->copyFile($src, $dest, $force);
        } else {
            throw new FilesystemException('Trying to copy a non-exists path: ' . $src);
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
    public function move(string $src, string $dest, bool $force = false): bool
    {
        // Check src path
        if (!is_readable($src)) {
            throw new FilesystemException('Cannot find source file: ' . $dest);
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
    public function delete(string $path): bool
    {
        $path = Path::clean(FileObject::unwrap($path));

        if (is_dir($path)) {
            // Delete children files
            $files = $this->files($path, true);

            /** @var FileObject $file */
            foreach ($files as $file) {
                $this->delete($file->getPathname());
            }

            // Delete children folders
            $folders = $this->folders($path, true);

            /** @var FileObject $folder */
            foreach ($folders as $folder) {
                $this->delete($folder->getPathname());
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
    public function files(string $path, bool $recursive = false): \Traversable
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

        return FilesIterator::create($path, $recursive)->filter($callback);
    }

    /**
     * folders
     *
     * @param   string  $path
     * @param   bool    $recursive
     *
     * @return  FilesIterator|FileObject[]
     */
    public function folders(string $path, bool $recursive = false): FilesIterator
    {
        /**
         * Files callback
         *
         * @param \SplFileInfo                $file  Current item's value
         * @param string                      $key      Current item's key
         * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
         *
         * @return boolean   TRUE to accept the current item, FALSE otherwise
         */
        $callback = static function (\SplFileInfo $file, $key, $iterator) use ($path, $recursive) {
            if ($recursive) {
                // Ignore self
                if ($file->getRealPath() === Path::normalize($path)) {
                    return false;
                }

                // If set to recursive, every returned folder name will include a dot (.),
                // so we can't using isDot() to detect folder.
                return $file->isDir() && ($file->getBasename() !== '..');
            }

            return $file->isDir() && !$file->isDot();
        };

        return FilesIterator::create($path, $recursive)->filter($callback);
    }

    /**
     * items
     *
     * @param   string  $path
     * @param   bool    $recursive
     *
     * @return  FilesIterator|FileObject[]
     */
    public function items($path, $recursive = false): FilesIterator
    {
        /**
         * Files callback
         *
         * @param \SplFileInfo                $file     Current item's value
         * @param string                      $key      Current item's key
         * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
         *
         * @return boolean   TRUE to accept the current item, FALSE otherwise
         */
        $callback = static function (\SplFileInfo $file, $key, $iterator) use ($path, $recursive) {
            if ($recursive) {
                // Ignore self
                $cPath = $file->isDir() ? $file->getPath() : $file->getPathname();

                if ($cPath === Path::clean($path)) {
                    return false;
                }

                // If set to recursive, every returned folder name will include a dot (.),
                // so we can't using isDot() to detect folder.
                return ($file->getBasename() !== '..');
            }

            return !$file->isDot();
        };

        return FilesIterator::create($path, $recursive)->filter($callback);
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
     * doAsync
     *
     * @param  string  $name
     * @param  array   $args
     *
     * @return  Promise
     */
    protected function doAsync(string $name, array $args = []): Promise
    {
        return new Promise(function ($resolve) use ($name, $args) {
            $resolve($this->$name(...$args));
        });
    }

    public function __call(string $name, $args)
    {
        $allows = [
            'mkdir',
            'copy',
            'move',
            'delete',
            'files',
            'folders',
            'items',
            'findOne',
            'find',
            'findBy'
        ];

        if (
            strpos($name, 'Async') !== false
            && in_array($method = substr($name, 0, -5), $allows, true)
        ) {
            return $this->doAsync($method, $args);
        }
    }
}
