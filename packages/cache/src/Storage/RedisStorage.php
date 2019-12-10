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
    public function get(string $key, array $options = [])
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
    public function clear(): void
    {
        $this->connect();

        $this->driver->flushall();
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): void
    {
        $this->connect();

        $this->driver->del($key);
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, array $options = []): void
    {
        $this->connect();

        if (!$this->driver->set($key, $value)) {
            return;
        }

        $ttl = $options['ttl'] ?? null;

        if ($ttl) {
            $this->driver->expire($key, $ttl);
        }
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
