<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Filesystem;

use League\Flysystem\File;

/**
 * The FileObject class.
 */
class FileObject
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $pathname;

    /**
     * @var array
     */
    protected $metadata;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * FileObject constructor.
     *
     * @param  Filesystem  $filesystem
     * @param  string      $path
     */
    public function __construct(Filesystem $filesystem, string $path)
    {
        $this->filesystem = $filesystem;
        $this->path       = $path;
    }

    /**
     * isFile
     *
     * @return  bool
     */
    public function isFile(): bool
    {
        return $this->getMetadata()['type'] === 'file';
    }

    /**
     * isDir
     *
     * @return  bool
     */
    public function isDir(): bool
    {
        return $this->getMetadata()['type'] === 'dir';
    }

    /**
     * getType
     *
     * @return  string
     */
    public function getType(): string
    {
        return $this->getMimetype()['type'];
    }

    /**
     * Check whether the file exists.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->filesystem->has($this->path);
    }

    /**
     * Read the file.
     *
     * @return string|false file contents
     */
    public function read()
    {
        return $this->filesystem->read($this->path);
    }

    /**
     * Read the file as a stream.
     *
     * @return resource|false file stream
     */
    public function readStream()
    {
        return $this->filesystem->readStream($this->path);
    }

    /**
     * Write the new file.
     *
     * @param string $content
     *
     * @return bool success boolean
     */
    public function write($content)
    {
        return $this->filesystem->write($this->path, $content);
    }

    /**
     * Write the new file using a stream.
     *
     * @param resource $resource
     *
     * @return bool success boolean
     */
    public function writeStream($resource)
    {
        return (bool) $this->filesystem->writeStream($this->path, $resource);
    }

    /**
     * Update the file contents.
     *
     * @param string $content
     *
     * @return bool success boolean
     */
    public function update($content)
    {
        return $this->filesystem->update($this->path, $content);
    }

    /**
     * Update the file contents with a stream.
     *
     * @param resource $resource
     *
     * @return bool success boolean
     */
    public function updateStream($resource)
    {
        return $this->filesystem->updateStream($this->path, $resource);
    }

    /**
     * Create the file or update if exists.
     *
     * @param string $content
     *
     * @return bool success boolean
     */
    public function put($content)
    {
        return $this->filesystem->put($this->path, $content);
    }

    /**
     * Create the file or update if exists using a stream.
     *
     * @param resource $resource
     *
     * @return bool success boolean
     */
    public function putStream($resource)
    {
        return $this->filesystem->putStream($this->path, $resource);
    }

    /**
     * Rename the file.
     *
     * @param string $newpath
     *
     * @return bool success boolean
     */
    public function rename($newpath)
    {
        if ($this->filesystem->rename($this->path, $newpath)) {
            $this->path = $newpath;

            return true;
        }

        return false;
    }

    /**
     * Copy the file.
     *
     * @param string $newpath
     *
     * @return static|false new file or false
     */
    public function copy($newpath)
    {
        if ($this->filesystem->copy($this->path, $newpath)) {
            return new static($this->filesystem, $newpath);
        }

        return false;
    }

    /**
     * Get the file's timestamp.
     *
     * @return string|false The timestamp or false on failure.
     */
    public function getTimestamp()
    {
        return $this->filesystem->getTimestamp($this->path);
    }

    /**
     * Get the file's mimetype.
     *
     * @return string|false The file mime-type or false on failure.
     */
    public function getMimetype()
    {
        return $this->filesystem->getMimetype($this->path);
    }

    /**
     * Get the file's visibility.
     *
     * @return string|false The visibility (public|private) or false on failure.
     */
    public function getVisibility()
    {
        return $this->filesystem->getVisibility($this->path);
    }

    /**
     * Get the file's metadata.
     *
     * @return array|false The file metadata or false on failure.
     */
    public function getMetadata()
    {
        return $this->filesystem->getMetadata($this->path);
    }

    /**
     * Get the file size.
     *
     * @return int|false The file size or false on failure.
     */
    public function getSize()
    {
        return $this->filesystem->getSize($this->path);
    }

    /**
     * Delete the file.
     *
     * @return bool success boolean
     */
    public function delete()
    {
        return $this->filesystem->delete($this->path);
    }

    /**
     * getFilInfo
     *
     * @return  \SplFileInfo
     */
    public function getFilInfo(): \SplFileInfo
    {
        return new \SplFileInfo($this->path);
    }

    /**
     * __call
     *
     * @param  string  $name
     * @param  array   $args
     *
     * @return  mixed
     */
    public function __call(string $name, array $args)
    {
        if (method_exists(\SplFileInfo::class, $name)) {
            return $this->getFilInfo()->$name(...$args);
        }

        throw new \BadMethodCallException(sprintf(
            '%s::%s() not found',
            static::class,
            $name
        ));
    }
}
