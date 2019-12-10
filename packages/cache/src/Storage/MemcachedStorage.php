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
    public function get(string $key, array $options = [])
    {
        $this->connect();

        $value = $this->driver->get($key);
        $code = $this->driver->getResultCode();

        if ($code === \Memcached::RES_SUCCESS) {
            return null;
        }

        return $value;
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
    public function clear(): void
    {
        $this->connect();

        $this->driver->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): void
    {
        $this->connect();

        $this->driver->delete($key);
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, array $options = []): void
    {
        $this->connect();

        $ttl = $options['ttl'] ?? null;

        $this->driver->set($key, $value, $ttl);
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

        $this->driver->setOption(\Memcached::OPT_COMPRESSION, false);
        $this->driver->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

        return $this;
    }
}
