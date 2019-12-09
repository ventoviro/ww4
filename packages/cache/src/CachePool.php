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
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Windwalker\Cache\Exception\RuntimeException;

/**
 * The Pool class.
 */
class CachePool implements CacheItemPoolInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $storage;

    /**
     * @var bool
     */
    protected $commiting = false;

    /**
     * @var array
     */
    protected $deferredItems = [];

    /**
     * CachePool constructor.
     *
     * @param $storage
     */
    public function __construct($storage)
    {
        $this->storage = $storage;

        $this->logger = new NullLogger();
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        return new CacheItem($key, function () {
            return $this->storage->get();
        });
    }

    /**
     * @inheritDoc
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
            $this->storage->save($item->getKey(), $item->get());

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
}
