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
use Windwalker\Scalars\StringObject;
use function Windwalker\arr;

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

    public function testDump()
    {
        self::assertEquals([1, 2, 3], $this->instance->dump());

        // Recursive
        $foo = arr([
            clone $this->instance,
            clone $this->instance,
        ]);

        self::assertEquals(
            [
                [1, 2, 3],
                [1, 2, 3],
            ],
            $foo->dump(true)
        );
    }

    public function testConstruct(): void
    {
        self::assertEquals([1, 2, 3], $this->instance->dump());
    }

    /**
     * testColumn
     *
     * @param  array       $src
     * @param  string|int  $column
     * @param  array       $except
     * @param  string      $message
     *
     * @return  void
     *
     * @dataProvider providerTestColumn
     */
    public function testColumn(array $src, $column, array $except, string $message): void
    {
        self::assertEquals($except, arr($src)->column($column)->dump(), $message);
    }

    public function providerTestColumn(): array
    {
        return [
            'generic array' => [
                [
                    [1, 2, 3, 4, 5],
                    [6, 7, 8, 9, 10],
                    [11, 12, 13, 14, 15],
                    [16, 17, 18, 19, 20],
                ],
                2,
                [3, 8, 13, 18],
                'Should get column #2',
            ],
            'associative array' => [
                [
                    [
                        'one' => 1,
                        'two' => 2,
                        'three' => 3,
                        'four' => 4,
                        'five' => 5,
                    ],
                    [
                        'one' => 6,
                        'two' => 7,
                        'three' => 8,
                        'four' => 9,
                        'five' => 10,
                    ],
                    [
                        'one' => 11,
                        'two' => 12,
                        'three' => 13,
                        'four' => 14,
                        'five' => 15,
                    ],
                    [
                        'one' => 16,
                        'two' => 17,
                        'three' => 18,
                        'four' => 19,
                        'five' => 20,
                    ],
                ],
                'four',
                [
                    4,
                    9,
                    14,
                    19,
                ],
                'Should get column \'four\'',
            ],
            'object array' => [
                [
                    (object) [
                        'one' => 1,
                        'two' => 2,
                        'three' => 3,
                        'four' => 4,
                        'five' => 5,
                    ],
                    (object) [
                        'one' => 6,
                        'two' => 7,
                        'three' => 8,
                        'four' => 9,
                        'five' => 10,
                    ],
                    (object) [
                        'one' => 11,
                        'two' => 12,
                        'three' => 13,
                        'four' => 14,
                        'five' => 15,
                    ],
                    (object) [
                        'one' => 16,
                        'two' => 17,
                        'three' => 18,
                        'four' => 19,
                        'five' => 20,
                    ],
                ],
                'four',
                [
                    4,
                    9,
                    14,
                    19,
                ],
                'Should get column \'four\'',
            ]
        ];
    }

    public function testIndexOf(): void
    {
        self::assertEquals(1, $this->instance->indexOf(2));
        self::assertEquals(-1, $this->instance->indexOf(8));
    }

    public function testPipe(): void
    {
        $a = $this->instance->pipe(fn (ArrayObject $arr) => $arr->append(4));

        self::assertEquals([1, 2, 3, 4], $a->dump());
    }

    public function testUnset(): void
    {
        unset($this->instance[1]);

        self::assertEquals([1, 3], $this->instance->values()->dump());

        $a = $this->getAssoc();

        unset($a['flower']);

        self::assertEquals(['foo' => 'bar'], $a->dump());

        $a = $this->getAssoc();

        unset($a->flower);

        self::assertEquals(['foo' => 'bar'], $a->dump());
    }

    public function testUnique(): void
    {
        $a = arr([1, 2, 1, 2, 4, 5, 6, 5, 6, 3, 2, 4, 5])->unique();

        self::assertEquals([
            0 => 1,
            1 => 2,
            4 => 4,
            5 => 5,
            6 => 6,
            9 => 3,
        ], $a->dump());

        $a = arr([
            [1, 2],
            [3, 4],
            [1, 2]
        ])->unique(SORT_REGULAR);

        self::assertEquals([
            [1, 2],
            [3, 4],
        ], $a->dump());
    }

    public function testExplode(): void
    {
        $a = ArrayObject::explode(',', '1,2,3');

        self::assertEquals([1, 2, 3], $a->dump());
    }

    public function testSum(): void
    {
        self::assertEquals(6, $this->instance->sum());

        self::assertEquals(0, $this->getAssoc()->sum());
    }

    public function testValues(): void
    {
        self::assertEquals(['bar', 'sakura'], $this->getAssoc()->values()->dump());
    }

    /**
     * testSearch
     *
     * @param  mixed  $exp
     * @param  mixed $search
     * @param  bool  $strict
     *
     * @return  void
     *
     * @dataProvider providerTestSearch
     */
    public function testSearch($exp, $search, bool $strict): void
    {
        $a = arr([1, 2, 3, true, false, '', 0, null]);

        self::assertEquals($exp, $a->search($search, $strict));
    }

    public function providerTestSearch(): array
    {
        return [
            [1, 2, false],
            [0, true, false],
            [3, true, true],
            [4, false, true],
            [4, null, false],
            [7, null, true],
        ];
    }

    public function testOffsetGet(): void
    {
        self::assertEquals(3, $this->instance[2]);

        self::assertNull($this->instance[5]);
    }

    public function testToArray(): void
    {
        $a = $this->instance->toArray();

        self::assertEquals($a->dump(), $this->instance->dump());
        self::assertNotSame($a, $this->instance);
    }

    public function testApply(): void
    {
        $a = $this->instance->apply(fn (array $v) => array_reverse($v));

        self::assertEquals([3, 2, 1], $a->dump());
    }

    public function testImplode(): void
    {
        $str = $this->instance->implode(',');

        self::assertEquals('1,2,3', (string) $str);
    }

    public function testKeys(): void
    {
        self::assertEquals([0, 1, 2], $this->instance->keys()->dump());
        self::assertEquals(['foo', 'flower'], $this->getAssoc()->keys()->dump());

        $array = arr(['blue', 'red', 'green', 'blue', 'blue']);

        self::assertEquals([0, 3, 4], $array->keys('blue')->dump());

        $array = arr([1, '1', 2, '2', 1, '1']);

        self::assertEquals([0, 1, 4, 5], $array->keys(1)->dump());
        self::assertEquals([0, 4], $array->keys(1, true)->dump());
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
        $a = $this->getAssoc();

        self::assertEquals('bar', $a->foo);
        self::assertNull($a->hello);
    }

    public function testOffsetExists()
    {
        self::assertTrue(isset($this->instance[2]));
        self::assertTrue(isset($this->getAssoc()['foo']));
        self::assertFalse(isset($this->getAssoc()['hello']));
    }

    public function testToString()
    {
        $this->expectException(\TypeError::class);

        $this->instance->toString();
    }

    public function testMagicIsset()
    {
        self::assertTrue(isset($this->instance->{2}));
        self::assertTrue(isset($this->getAssoc()->foo));
        self::assertFalse(isset($this->getAssoc()->hello));
    }
}