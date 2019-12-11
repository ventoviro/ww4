<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Storage;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

/**
 * The FlysystemStorage class.
 */
class FlysystemStorage extends FileStorage
{
    /**
     * @var Filesystem
     */
    protected $driver;

    /**
     * @var array
     */
    protected $options;

    /**
     * FlysystemStorage constructor.
     *
     * @param  Filesystem  $driver
     * @param  array       $options
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function __construct(Filesystem $driver, array $options = [])
    {
        $this->driver = $driver;

        parent::__construct($driver->getMetadata('/')['path'], $options);
    }

    /**
     * @inheritDoc
     */
    protected function read(string $key): string
    {
        return (string) $this->getDriver()->read($key);
    }

    /**
     * @inheritDoc
     */
    protected function write(string $key, string $value): bool
    {
        return $this->getDriver()->write($key, $value);
    }

    /**
     * @inheritDoc
     */
    protected function exists(string $key): bool
    {
        return $this->getDriver()->has($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $results = true;

        foreach ($this->getDriver()->listContents('/', true) as $metadata) {
            $results = $this->getDriver()->delete($metadata['path']) && $results;
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): bool
    {
        return $this->getDriver()->delete($key);
    }

    /**
     * @inheritDoc
     */
    protected function checkFilePath($filePath): bool
    {
        return true;
    }

    /**
     * Method to get property Driver
     *
     * @return  FilesystemInterface
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getDriver(): FilesystemInterface
    {
        return $this->driver;
    }

    /**
     * Method to set property driver
     *
     * @param  FilesystemInterface  $driver
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setDriver(FilesystemInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }
}
