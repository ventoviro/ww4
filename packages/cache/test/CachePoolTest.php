<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Cache\CachePool;

/**
 * The CachePoolTest class.
 */
class CachePoolTest extends TestCase
{
    /**
     * @var CachePool
     */
    protected $instance;

    /**
     * testBasicUsage
     *
     * @return  void
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testBasicUsage(): void
    {
        $pool = new CachePool();

        $item = $pool->getItem('hello');

        self::assertFalse($item->isHit());

        if (!$item->isHit()) {
            $value = 'Hello World';

            $pool->save($item->set($value));
        }

        self::assertEquals('Hello World', $item->get());
    }

    /**
     * @see  CachePool::setStorage
     */
    public function testSetStorage(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::getSerializer
     */
    public function testGetSerializer(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::setSerializer
     */
    public function testSetSerializer(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::clear
     */
    public function testClear(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::getStorage
     */
    public function testGetStorage(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::__construct
     */
    public function testConstruct(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::commit
     */
    public function testCommit(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::save
     */
    public function testSave(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::getItem
     */
    public function testGetItem(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::saveDeferred
     */
    public function testSaveDeferred(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::deleteItems
     */
    public function testDeleteItems(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::getItems
     */
    public function testGetItems(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::deleteItem
     */
    public function testDeleteItem(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::hasItem
     */
    public function testHasItem(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = null;
    }

    protected function tearDown(): void
    {
    }
}
