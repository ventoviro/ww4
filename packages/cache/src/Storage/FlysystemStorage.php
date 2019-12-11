<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Storage;

use League\Flysystem\FilesystemInterface;

/**
 * The FlysystemStorage class.
 */
class FlysystemStorage implements StorageInterface
{
    /**
     * @var FilesystemInterface
     */
    protected $driver;

    /**
     * FlysystemStorage constructor.
     *
     * @param  FilesystemInterface  $driver
     */
    public function __construct(FilesystemInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, array $options = [])
    {
        return $this->getDriver()->read($key);
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->getDriver()->has($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        foreach ($this->getDriver()->listContents('/', true) as $metadata) {
            $this->getDriver()->delete($metadata['path']);
        }
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): void
    {
        $this->getDriver()->delete($key);
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, array $options = []): void
    {
        $this->getDriver()->write($key, $value, $options);
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
