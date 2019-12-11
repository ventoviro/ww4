<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use Windwalker\Cache\Exception\RuntimeException;
use Windwalker\Cache\Serializer\RawSerializer;
use Windwalker\Cache\Serializer\SerializerInterface;
use Windwalker\Cache\Storage\ArrayStorage;
use Windwalker\Cache\Storage\StorageInterface;

/**
 * The Pool class.
 */
class CachePool implements CacheItemPoolInterface, CacheInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var bool
     */
    protected $commiting = false;

    /**
     * @var array
     */
    protected $deferredItems = [];

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * CachePool constructor.
     *
     * @param  StorageInterface     $storage
     * @param  SerializerInterface  $serializer
     */
    public function __construct(?StorageInterface $storage = null, ?SerializerInterface $serializer = null)
    {
        $this->storage = $storage ?? new ArrayStorage();
        $this->serializer = $serializer ?? new RawSerializer();

        $this->logger = new NullLogger();
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        $item = new CacheItem($key);
        $item->setLogger($this->logger);

        if (!$this->storage->has($key)) {
            return $item;
        }

        $item->set($this->storage->get($key));

        return $item;
    }

    /**
     * @inheritDoc
     *
     * @return \Traversable|CacheItemInterface[]
     */
    public function getItems(array $keys = [])
    {
        foreach ($keys as $key) {
            yield $key => $this->getItem($key);
        }
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key)
    {
        return $this->getItem($key)->isHit();
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        try {
            $this->storage->clear();

            return true;
        } catch (RuntimeException $e) {
            $this->logException(
                'Clearing cache pool caused exception.',
                $e
            );
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteItem($key)
    {
        try {
            $this->storage->remove($key);

            return true;
        } catch (RuntimeException $e) {
            $this->logException(
                'Deleting cache item caused exception.',
                $e
            );
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys)
    {
        $results = true;

        foreach ($keys as $key) {
            $results = $this->deleteItem($key) && $results;
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item)
    {
        try {
            $this->storage->save($item->getKey(), $this->serializer->serialize($item->get()));

            return true;
        } catch (RuntimeException $e) {
            $this->logException(
                'Retrieving cache item caused exception.',
                $e,
                $item
            );
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        while ($this->commiting) {
            usleep(1);
        }

        $this->deferredItems[] = $item;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function commit()
    {
        $this->commiting = true;

        foreach ($this->deferredItems as $key => $item) {
            $this->save($item);

            if ($item->isHit()) {
                unset($this->deferredItems[$key]);
            }
        }

        $result = !count($this->deferredItems);

        $this->commiting = false;

        return $result;
    }

    /**
     * logException
     *
     * @param  string                   $message
     * @param  \Throwable               $e
     * @param  CacheItemInterface|null  $item
     *
     * @return  void
     */
    protected function logException(string $message, \Throwable $e, ?CacheItemInterface $item = null): void
    {
        $this->logger->critical(
            $message,
            array(
                'exception' => $e,
                'key' => $item ? $item->getKey() : null
            )
        );
    }

    /**
     * Method to get property Storage
     *
     * @return  StorageInterface
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * Method to set property storage
     *
     * @param  StorageInterface  $storage
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Method to get property Serializer
     *
     * @return  SerializerInterface
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * Method to set property serializer
     *
     * @param  SerializerInterface  $serializer
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        $item = $this->getItem($key);

        if (!$item->isHit()) {
            return $default;
        }

        return $item->get();
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        $item = $this->getItem($key);

        $item->expiresAfter($ttl);
        $item->set($value);

        return $this->save($item);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        return $this->deleteItem($key);
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        foreach ($this->getItems($keys) as $item) {
            yield $item->get();
        }
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null)
    {
        $results = true;

        foreach ($values as $key => $value) {
            $results = $this->set($key, $value, $ttl) && $results;
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys)
    {
        $results = true;

        foreach ($keys as $key) {
            $results = $this->delete($key) && $results;
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return $this->hasItem($key);
    }
}
