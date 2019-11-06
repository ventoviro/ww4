<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Utilities;

use PHPUnit\Framework\TestCase;

/**
 * The ArrConverterTest class.
 *
 * @since  {DEPLOY_VERSION}
 */
class ArrConverterTest extends TestCase
{
    public function testFlipMatrix(): void
    {
        $src      = [
            'A' => [
                'id1',
                'Julius Caesar',
            ],
            'B' => [
                'id2',
                'title' => 'Macbeth',
            ],
            'C' => [
                'id3',
                'title' => 'Othello',
            ],
            'D' => [
                'id4',
                'title' => 'Hamlet',
            ],
        ];
        $expected = [
            'id1' => 'A',
            'Julius Caesar' => 'A',
            'id2' => 'B',
            'Macbeth' => 'B',
            'id3' => 'C',
            'Othello' => 'C',
            'id4' => 'D',
            'Hamlet' => 'D',
        ];

        self::assertEquals($expected, ArrConverter::flipMatrix($src));
    }

    /**
     * testGroup
     *
     * @param $source
     * @param $key
     * @param $expected
     * @param $type
     *
     * @dataProvider  providerTestGroup
     */
    public function testGroup($source, $key, $expected, int $type)
    {
        self::assertEquals($expected, ArrConverter::group($source, $key, $type));
    }

    /**
     * providerTestGroup
     *
     * @return  array
     */
    public function providerTestGroup()
    {
        return [
            'A scalar array' => [
                // Source
                [
                    1 => 'a',
                    2 => 'b',
                    3 => 'b',
                    4 => 'c',
                    5 => 'a',
                    6 => 'a',
                ],
                // Key
                null,
                // Expected
                [
                    'a' => [1, 5, 6],
                    'b' => [2, 3],
                    'c' => 4,
                ],
                ArrConverter::GROUP_TYPE_MIX
            ],
            'A scalar array force child array' => [
                // Source
                [
                    1 => 'a',
                    2 => 'b',
                    3 => 'b',
                    4 => 'c',
                    5 => 'a',
                    6 => 'a',
                ],
                // Key
                null,
                // Expected
                [
                    'a' => [1, 5, 6],
                    'b' => [2, 3],
                    'c' => [4],
                ],
                ArrConverter::GROUP_TYPE_ARRAY
            ],
            'An array of associative arrays' => [
                // Source
                [
                    1 => ['id' => 41, 'title' => 'boo'],
                    2 => ['id' => 42, 'title' => 'boo'],
                    3 => ['title' => 'boo'],
                    4 => ['id' => 42, 'title' => 'boo'],
                    5 => ['id' => 43, 'title' => 'boo'],
                ],
                // Key
                'id',
                // Expected
                [
                    41 => ['id' => 41, 'title' => 'boo'],
                    42 => [
                        ['id' => 42, 'title' => 'boo'],
                        ['id' => 42, 'title' => 'boo'],
                    ],
                    43 => ['id' => 43, 'title' => 'boo'],
                ],
                ArrConverter::GROUP_TYPE_MIX
            ],
            'An array of associative arrays but use key by' => [
                // Source
                [
                    1 => ['id' => 41, 'title' => 'boo'],
                    2 => ['id' => 42, 'title' => 'boo1'],
                    3 => ['title' => 'boo'],
                    4 => ['id' => 42, 'title' => 'boo2'],
                    5 => ['id' => 43, 'title' => 'boo'],
                ],
                // Key
                'id',
                // Expected
                [
                    41 => ['id' => 41, 'title' => 'boo'],
                    42 => ['id' => 42, 'title' => 'boo2'],
                    43 => ['id' => 43, 'title' => 'boo'],
                ],
                ArrConverter::GROUP_TYPE_KEY_BY
            ],
            'An array of associative arrays force child array' => [
                // Source
                [
                    1 => ['id' => 41, 'title' => 'boo'],
                    2 => ['id' => 42, 'title' => 'boo'],
                    3 => ['title' => 'boo'],
                    4 => ['id' => 42, 'title' => 'boo'],
                    5 => ['id' => 43, 'title' => 'boo'],
                ],
                // Key
                'id',
                // Expected
                [
                    41 => [['id' => 41, 'title' => 'boo']],
                    42 => [
                        ['id' => 42, 'title' => 'boo'],
                        ['id' => 42, 'title' => 'boo'],
                    ],
                    43 => [['id' => 43, 'title' => 'boo']],
                ],
                ArrConverter::GROUP_TYPE_ARRAY
            ],
            'An array of objects' => [
                // Source
                [
                    1 => (object) ['id' => 41, 'title' => 'boo'],
                    2 => (object) ['id' => 42, 'title' => 'boo'],
                    3 => (object) ['title' => 'boo'],
                    4 => (object) ['id' => 42, 'title' => 'boo'],
                    5 => (object) ['id' => 43, 'title' => 'boo'],
                ],
                // Key
                'id',
                // Expected
                [
                    41 => (object) ['id' => 41, 'title' => 'boo'],
                    42 => [
                        (object) ['id' => 42, 'title' => 'boo'],
                        (object) ['id' => 42, 'title' => 'boo'],
                    ],
                    43 => (object) ['id' => 43, 'title' => 'boo'],
                ],
                ArrConverter::GROUP_TYPE_MIX
            ],
        ];
    }

    public function testTranspose(): void
    {
        $src = [
            [
                'value' => 'aaa',
                'text' => 'aaa'
            ],
            [
                'value' => 'bbb',
                'text' => 'bbb'
            ],
            [
                'value' => 'ccc',
                'text' => 'ccc'
            ],
        ];

        $expected = [
            'value' => [
                0 => 'aaa',
                1 => 'bbb',
                2 => 'ccc',
            ],
            'text' => [
                0 => 'aaa',
                1 => 'bbb',
                2 => 'ccc',
            ],
        ];

        self::assertEquals($expected, ArrConverter::transpose($src));
    }

    public function testGroupPrefix(): void
    {
        $src = [
            'id' => 123,
            'title' => 'Hello',
            'params_foo' => 'Foo',
            'params_bar' => 'Bar',
            'params_yoo' => 'Yoo',
        ];

        $exp = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'yoo' => 'Yoo',
        ];

        self::assertEquals($exp, ArrConverter::groupPrefix($src, 'params_'));
        self::assertEquals($exp, ArrConverter::groupPrefix($src, 'params_', true));
        self::assertEquals([
            'id' => 123,
            'title' => 'Hello',
        ], $src);
    }

    public function testExtractPrefix(): void
    {
        $item = [
            'id' => 123,
            'title' => 'Hello',
        ];

        $src = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'yoo' => 'Yoo',
        ];

        self::assertEquals([
            'params_foo' => 'Foo',
            'params_bar' => 'Bar',
            'params_yoo' => 'Yoo',
        ], ArrConverter::extractPrefix($src, 'params_'));

        self::assertEquals([
            'id' => 123,
            'title' => 'Hello',
            'params_foo' => 'Foo',
            'params_bar' => 'Bar',
            'params_yoo' => 'Yoo',
        ], ArrConverter::extractPrefix($src, 'params_', $item));
    }
}
