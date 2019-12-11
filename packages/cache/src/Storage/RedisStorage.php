<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Storage;

use Redis;

/**
 * The RedisStorage class.
 */
class RedisStorage implements StorageInterface
{
    /**
     * Property defaultHost.
     *
     * @var  string
     */
    protected $defaultHost = '127.0.0.1';

    /**
     * Property defaultPort.
     *
     * @var  int
     */
    protected $defaultPort = 6379;

    /**
     * @var Redis
     */
    protected $driver;

    /**
     * RedisStorage constructor.
     *
     * @param $driver
     */
    public function __construct(?Redis $driver = null)
    {
        $this->driver = $driver;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        $this->connect();

        return $this->driver->get($key);
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        $this->connect();

        return (bool) $this->driver->exists($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->connect();

        return $this->driver->flushall();
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): bool
    {
        $this->connect();

        $this->driver->del($key);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, int $expiration = 0): bool
    {
        $this->connect();

        if (!$this->driver->set($key, $value)) {
            return false;
        }

        if ($expiration !== 0) {
            $ttl = $expiration - time();

            $this->driver->expire($key, $ttl);
        }

        return true;
    }

    /**
     * connect
     *
     * @return  static
     */
    protected function connect()
    {
        // We want to only create the driver once.
        if (isset($this->driver)) {
            return $this;
        }

        if (($this->defaultHost === 'localhost' || filter_var($this->defaultHost, FILTER_VALIDATE_IP))) {
            $this->driver->connect('tcp://' . $this->defaultHost . ':' . $this->defaultPort, $this->defaultPort);
        } else {
            $this->driver->connect($this->defaultHost, null);
        }

        return $this;
    }
}
