<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Scalars\ArrayObject;

/**
 * The ArrayObjectTest class.
 *
 * @since  {DEPLOY_VERSION}
 */
class ArrayObjectTest extends TestCase
{
    /**
     * Test instance.
     *
     * @var ArrayObject
     */
    protected ArrayObject $instance;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->instance = ArrayObject::range(1, 3);
    }

    protected function getAssoc(): ArrayObject
    {
        return new ArrayObject(['foo' => 'bar', 'flower' => 'sakura']);
    }

    public function testJsonSerialize(): void
    {
        self::assertEquals('[1,2,3]', json_encode($this->instance, JSON_THROW_ON_ERROR, 512));
    }

    public function testCount(): void
    {
        self::assertCount(3, $this->instance);
        self::assertEquals(3, $this->instance->count());
    }

    public function testGetIterator(): void
    {
        self::assertEquals([1, 2, 3], iterator_to_array($this->instance));
    }

    public function testContains(): void
    {
        self::assertTrue($this->instance->contains(1));
        self::assertTrue($this->instance->contains('2'));
        self::assertFalse($this->instance->contains('5'));
    }

    public function testKeyExists()
    {
        self::assertTrue($this->instance->keyExists(1));
        self::assertFalse($this->instance->keyExists(3));

        $foo = $this->getAssoc();

        self::assertTrue($foo->keyExists('foo'));
        self::assertFalse($foo->keyExists('car'));
    }

    public function testUnderscoreSet(): void
    {
        $this->instance->__set(3, 4);

        self::assertEquals([1, 2, 3, 4], $this->instance->dump());

        $this->instance->foo = 'bar';

        self::assertEquals([1, 2, 3, 4, 'foo' => 'bar'], $this->instance->dump());
    }

    public function testBind()
    {

    }

    public function testDump()
    {

    }

    public function test__construct()
    {

    }

    public function testColumn()
    {

    }

    public function testIndexOf()
    {

    }

    public function testPipe()
    {

    }

    public function test__unset()
    {

    }

    public function testOffsetUnset()
    {

    }

    public function testUnique()
    {

    }

    public function testExplode()
    {

    }

    public function testSum()
    {

    }

    public function testValues()
    {

    }

    public function testSearch()
    {

    }

    public function testOffsetGet()
    {

    }

    public function testToArray()
    {

    }

    public function testApply()
    {

    }

    public function testImplode()
    {

    }

    public function testKeys()
    {

    }

    public function testOffsetSet(): void
    {
        $a = $this->getAssoc();

        $a->offsetSet('roo', 'koo');

        self::assertEquals(['foo' => 'bar', 'flower' => 'sakura', 'roo' => 'koo'], $a->dump());

        $a['roo'] = 'goo';

        self::assertEquals(['foo' => 'bar', 'flower' => 'sakura', 'roo' => 'goo'], $a->dump());

        $a = $this->instance;

        $a[] = 'hello';

        self::assertEquals([1, 2, 3, 'hello'], $a->dump());
    }

    public function testMagicGet()
    {

    }

    public function testOffsetExists()
    {

    }

    public function testToString()
    {

    }

    public function test__isset()
    {

    }
}
