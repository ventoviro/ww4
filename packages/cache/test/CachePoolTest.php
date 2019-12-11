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
use Windwalker\Cache\Serializer\RawSerializer;
use Windwalker\Test\Traits\TestAccessorTrait;

/**
 * The CachePoolTest class.
 */
class CachePoolTest extends TestCase
{
    use TestAccessorTrait;

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
        $pool = $this->instance;

        $item = $pool->getItem('hello');

        self::assertFalse($item->isHit());

        if (!$item->isHit()) {
            $value = 'Hello World';

            $pool->save($item->set($value));
        }

        self::assertEquals('Hello World', $item->get());
    }

    /**
     * @see  CachePool::setSerializer
     */
    public function testGetSetSerializer(): void
    {
        $this->instance->setSerializer($ser = new RawSerializer());

        self::assertSame($ser, $this->instance->getSerializer());
    }

    /**
     * @see  CachePool::commit
     */
    public function testCommit(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::set
     */
    public function testSetGet(): void
    {
        $this->instance->set('hello', 'RRR');

        self::assertEquals('RRR', $this->instance->getStorage()->get('hello'));

        $this->instance->set('hello2', 'RRR2', -5);

        self::assertNull($this->instance->getStorage()->get('hello2'));

        $this->instance->set('hello3', 'RRR3', 10);

        self::assertEquals(
            time() + 10,
            $this->getValue($this->instance->getStorage(), 'storage')['hello3'][0]
        );
    }

    /**
     * @see  CachePool::deleteMultiple
     */
    public function testDeleteMultiple(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::setStorage
     */
    public function testSetStorage(): void
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

    /**
     * @see  CachePool::getItems
     */
    public function testGetItems(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::getMultiple
     */
    public function testGetMultiple(): void
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
     * @see  CachePool::deleteItems
     */
    public function testDeleteItems(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::setMultiple
     */
    public function testSetMultiple(): void
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
     * @see  CachePool::deleteItem
     */
    public function testDeleteItem(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::has
     */
    public function testHas(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::__destruct
     */
    public function test__destruct(): void
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
     * @see  CachePool::save
     */
    public function testSave(): void
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
     * @see  CachePool::delete
     */
    public function testDelete(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  CachePool::__construct
     */
    public function test__construct(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = new CachePool();
    }

    protected function tearDown(): void
    {
    }
}
