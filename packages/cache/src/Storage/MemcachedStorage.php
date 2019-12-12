<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Storage;

use Windwalker\Cache\Pool\MemcachedPool;

/**
 * The MemcachedStorage class.
 */
class MemcachedStorage implements StorageInterface
{
    /**
     * @var \Memcached
     */
    protected $driver;

    /**
     * MemcachedStorage constructor.
     *
     * @param  \Memcached  $driver
     */
    public function __construct(?\Memcached $driver = null)
    {
        if (!extension_loaded('memcached') || !class_exists('Memcached')) {
            throw new \RuntimeException('Memcached not supported.');
        }

        $this->driver = $driver;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        $this->connect();

        $value = $this->driver->get($key);
        $code = $this->driver->getResultCode();

        if ($code === \Memcached::RES_SUCCESS) {
            return $value;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        $this->connect();

        $this->driver->get($key);

        return $this->driver->getResultCode() !== \Memcached::RES_NOTFOUND;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->connect();

        $this->driver->flush();

        return $this->driver->getResultCode() === \Memcached::RES_SUCCESS;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): bool
    {
        $this->connect();

        $this->driver->delete($key);

        return $this->driver->getResultCode() === \Memcached::RES_SUCCESS;
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, int $expiration = 0): bool
    {
        $this->connect();

        $this->driver->set($key, $value, $expiration);

        return $this->driver->getResultCode() === \Memcached::RES_SUCCESS;
    }

    /**
     * connect
     *
     * @return  static
     */
    protected function connect()
    {
        // We want to only create the driver once.
        if ($this->driver) {
            return $this;
        }

        $this->driver = new \Memcached();
        $this->driver->addServer('localhost', 11211);

        $this->driver->setOption(\Memcached::OPT_COMPRESSION, false);
        $this->driver->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

        return $this;
    }
}
