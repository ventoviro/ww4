<?php
/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT.
 * @license    Please see LICENSE file.
 */

namespace Windwalker\Utilities\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Test\TestCase\AbstractBaseTestCase;
use Windwalker\Utilities\ArrayHelper;

/**
 * The ArrayHelperTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArrayHelperTest extends AbstractBaseTestCase
{
    /**
     * testDef
     *
     * @param array|object $array
     * @param string       $key
     * @param string       $value
     * @param mixed        $expected
     *
     * @return  void
     *
     * @dataProvider providerTestDef
     */
    public function testDef($array, $key, $value, $expected)
    {
        self::assertEquals($expected, $return = ArrayHelper::def($array, $key, $value));

        if (is_object($array)) {
            self::assertSame($array, $return);
            self::assertEquals($array, $expected);
        }
    }

    /**
     * providerTestDef
     *
     * @return  array
     */
    public function providerTestDef()
    {
        return [
            [
                ['foo' => 'bar'],
                'foo',
                'yoo',
                ['foo' => 'bar'],
            ],
            [
                ['foo' => 'bar'],
                'baz',
                'goo',
                ['foo' => 'bar', 'baz' => 'goo'],
            ],
            [
                (object) ['foo' => 'bar'],
                'foo',
                'yoo',
                (object) ['foo' => 'bar'],
            ],
            [
                (object) ['foo' => 'bar'],
                'baz',
                'goo',
                (object) ['foo' => 'bar', 'baz' => 'goo'],
            ]
        ];
    }

    /**
     * testHas
     *
     * @return  void
     */
    public function testHas()
    {
        self::assertTrue(ArrayHelper::has(['foo' => 'bar'], 'foo'));
        self::assertFalse(ArrayHelper::has(['foo' => 'bar'], 'yoo'));
        self::assertTrue(ArrayHelper::has(['foo' => ['bar' => 'yoo']], 'foo.bar'));
        self::assertTrue(ArrayHelper::has(['foo' => ['bar' => 'yoo']], 'foo/bar', '/'));
        self::assertFalse(ArrayHelper::has(['foo' => ['bar' => 'yoo']], ''));
    }

    /**
     * testCollapse
     *
     * @return  void
     */
    public function testCollapse()
    {
        $array = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ];

        self::assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], ArrayHelper::collapse($array));

        $array = [
            (object) [1, 2, 3],
            4,
            5,
            6,
            [7, 8, 9],
        ];

        self::assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], ArrayHelper::collapse($array));
    }

    /**
     * testFlatten
     *
     * @return  void
     */
    public function testFlatten()
    {
        $array = [
            'flower' => 'sakura',
            'olive' => 'peace',
            'pos1' => [
                'sunflower' => 'love'
            ],
            'pos2' => [
                'cornflower' => 'elegant',
                'pos3' => [
                    'olive'
                ]
            ]
        ];

        $flatted = ArrayHelper::flatten($array);

        $this->assertEquals($flatted['pos1.sunflower'], 'love');

        $flatted = ArrayHelper::flatten($array, '/');

        $this->assertEquals($flatted['pos1/sunflower'], 'love');

        // Test depth
        $flatted = ArrayHelper::flatten($array, '/', 0);

        $this->assertEquals($flatted['pos2/pos3/0'], 'olive');

        $flatted = ArrayHelper::flatten($array, '/', 1);

        $this->assertEquals($flatted['pos2']['pos3'], ['olive']);

        $flatted = ArrayHelper::flatten($array, '/', 2);

        $this->assertEquals($flatted['pos2/pos3'], ['olive']);

        $flatted = ArrayHelper::flatten($array, '/', 3);

        $this->assertEquals($flatted['pos2/pos3/0'], 'olive');

        $array = [
            'Apple' => [
                ['name' => 'iPhone 6S', 'brand' => 'Apple'],
            ],
            'Samsung' => [
                ['name' => 'Galaxy S7', 'brand' => 'Samsung']
            ],
        ];

        $expected = [
            'Apple.0' => ['name' => 'iPhone 6S', 'brand' => 'Apple'],
            'Samsung.0' => ['name' => 'Galaxy S7', 'brand' => 'Samsung'],
        ];

        $this->assertEquals($expected, ArrayHelper::flatten($array, '.', 2));
    }

    /**
     * testGet
     *
     * @return  void
     */
    public function testGet()
    {
        $data = [
            'flower' => 'sakura',
            'olive' => 'peace',
            'pos1' => [
                'sunflower' => 'love'
            ],
            'pos2' => [
                'cornflower' => 'elegant'
            ],
            'array' => [
                'A',
                'B',
                'C'
            ]
        ];

        $this->assertEquals('sakura', ArrayHelper::get($data, 'flower'));
        $this->assertEquals('love', ArrayHelper::get($data, 'pos1.sunflower'));
        $this->assertEquals('default', ArrayHelper::get($data, 'pos1.notexists', 'default'));
        $this->assertEquals('default', ArrayHelper::get($data, '', 'default'));
        $this->assertEquals('love', ArrayHelper::get($data, 'pos1/sunflower', null, '/'));
        $this->assertEquals($data['array'], ArrayHelper::get($data, 'array'));
        $this->assertNull(ArrayHelper::get($data, 'not.exists'));

        $data = (object) [
            'flower' => 'sakura',
            'olive' => 'peace',
            'pos1' => (object) [
                'sunflower' => 'love'
            ],
            'pos2' => new \ArrayObject([
                'cornflower' => 'elegant'
            ]),
            'array' => (object) [
                'A',
                'B',
                'C'
            ]
        ];

        $this->assertEquals('sakura', ArrayHelper::get($data, 'flower'));
        $this->assertEquals('love', ArrayHelper::get($data, 'pos1.sunflower'));
        $this->assertEquals('default', ArrayHelper::get($data, 'pos1.notexists', 'default'));
        $this->assertEquals('elegant', ArrayHelper::get($data, 'pos2.cornflower'));
        $this->assertEquals('love', ArrayHelper::get($data, 'pos1/sunflower', null, '/'));
        $this->assertEquals($data->array, ArrayHelper::get($data, 'array'));
        $this->assertNull(ArrayHelper::get($data, 'not.exists'));
    }

    public function testSet()
    {
        $data = array();

        // One level
        $return = ArrayHelper::set($data, 'flower', 'sakura');

        $this->assertEquals('sakura', $data['flower']);
        $this->assertTrue($return);

        // Multi-level
        ArrayHelper::set($data, 'foo.bar', 'test');

        $this->assertEquals('test', $data['foo']['bar']);

        // Separator
        ArrayHelper::set($data, 'foo/bar', 'play', '/');

        $this->assertEquals('play', $data['foo']['bar']);

        // Type
        ArrayHelper::set($data, 'cloud/fly', 'bird', '/', 'stdClass');

        $this->assertEquals('bird', $data['cloud']->fly);

        // False
        $return = ArrayHelper::set($data, '', 'goo');

        $this->assertFalse($return);

        // Fix path
        ArrayHelper::set($data, 'double..separators', 'value');

        $this->assertEquals('value', $data['double']['separators']);

        $this->assertExpectedException(function () {
            ArrayHelper::set($data, 'a.b', 'c', '.', 'Non\Exists\Class');
        }, \InvalidArgumentException::class, 'Type or class: Non\Exists\Class not exists');
    }

    /**
     * testRemove
     *
     * @param array|object $array
     * @param array|object $expected
     * @param int|string   $offset
     * @param string       $separator
     *
     * @dataProvider providerTestRemove
     */
    public function testRemove($array, $expected, $offset, $separator)
    {
        $actual = ArrayHelper::remove($array, $offset, $separator);

        self::assertEquals($expected, $actual);

        if (is_object($array)) {
            self::assertSame($array, $actual);
            self::assertTrue(is_object($actual));
        }
    }

    /**
     * providerTestRemove
     *
     * @return  array
     */
    public function providerTestRemove()
    {
        return [
            [
                [1, 2, 3],
                [0 => 1, 2 => 3],
                1,
                '.'
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
                5,
                '.'
            ],
            [
                ['foo' => 'bar', 'baz' => 'yoo'],
                ['baz' => 'yoo'],
                'foo',
                '.'
            ],
            [
                ['foo' => 'bar', 'baz' => 'yoo'],
                ['foo' => 'bar', 'baz' => 'yoo'],
                'haa',
                '.'
            ],
            [
                ['foo' => 'bar', 'baz' => ['joo' => 'hoo']],
                ['foo' => 'bar', 'baz' => []],
                'baz.joo',
                '.'
            ],
            [
                (object) ['foo' => 'bar', 'baz' => 'yoo'],
                (object) ['baz' => 'yoo'],
                'foo',
                '.'
            ],
            [
                (object) ['foo' => 'bar', 'baz' => 'yoo'],
                (object) ['foo' => 'bar', 'baz' => 'yoo'],
                'haa',
                '.'
            ],
            [
                (object) ['foo' => 'bar', 'baz' => ['joo' => 'hoo']],
                (object) ['foo' => 'bar', 'baz' => []],
                'baz/joo',
                '/'
            ],
        ];
    }

    public function testKeep()
    {
        self::markTestIncomplete();
    }

    public function testFind()
    {
        self::markTestIncomplete();
    }

    public function testFindFirst()
    {
        self::markTestIncomplete();
    }

    public function testPluck()
    {
        self::markTestIncomplete();
    }

    public function testTakeout()
    {
        self::markTestIncomplete();
    }

    public function testSort()
    {
        self::markTestIncomplete();
    }

    public function testSortRecursive()
    {
        self::markTestIncomplete();
    }

    public function testToArray()
    {
        self::markTestIncomplete();
    }

    public function testToObject()
    {
        self::markTestIncomplete();
    }

    public function testInvert()
    {
        self::markTestIncomplete();
    }

    public function testIsAssociative()
    {
        self::markTestIncomplete();
    }

    public function testGroup()
    {
        self::markTestIncomplete();
    }

    public function testUnique()
    {
        self::markTestIncomplete();
    }

    public function testMerge()
    {
        self::markTestIncomplete();
    }

    public function testDump()
    {
        self::markTestIncomplete();
    }

    public function testMatch()
    {
        self::markTestIncomplete();
    }

    public function testMap()
    {
        self::markTestIncomplete();
    }
}
