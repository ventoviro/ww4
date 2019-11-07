<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars\Test\Concern;

use PHPUnit\Framework\TestCase;
use Windwalker\Scalars\ArrayObject;
use function Windwalker\arr;

/**
 * The ArrayCreationTraitTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArrayCreationTraitTest extends TestCase
{
    protected ?ArrayObject $instance;

    public function testCombine(): void
    {
        self::assertEquals(
            [
                1 => 'foo',
                2 => 'bar',
                3 => 'yoo',
            ],
            $this->instance->combine(['foo', 'bar', 'yoo'])->dump()
        );

        self::assertEquals(
            [
                1 => 'foo',
                2 => 'bar',
                3 => 'yoo',
            ],
            $this->instance->combine(arr(['foo', 'bar', 'yoo']))->dump()
        );
    }

    public function testMergeRecursive(): void
    {
        $data1 = arr([
            'green' => 'Hulk',
            'red' => 'empty',
            'human' => [
                'dark' => 'empty',
                'black' => [
                    'male' => 'empty',
                    'female' => 'empty',
                    'no-gender' => 'empty',
                ],
            ]
        ]);

        $data2 = [
            'ai' => 'Jarvis',
            'agent' => 'Phil Coulson',
            'red' => [
                'left' => 'Pepper',
                'right' => 'Iron Man',
            ],
            'human' => [
                'dark' => 'Nick Fury',
                'black' => [
                    'female' => 'Black Widow',
                    'male' => 'Loki',
                ],
            ]
        ];

        $data3 = arr([
            'ai' => 'Ultron'
        ]);

        $expected = [
            'ai' => 'Jarvis',
            'agent' => 'Phil Coulson',
            'green' => 'Hulk',
            'red' => [
                'left' => 'Pepper',
                'right' => 'Iron Man',
            ],
            'human' => [
                'dark' => 'Nick Fury',
                'black' => [
                    'male' => 'Loki',
                    'female' => 'Black Widow',
                    'no-gender' => 'empty',
                ],
            ],
        ];

        $this->assertEquals($expected, $data1->mergeRecursive($data1, $data2)->dump());

        $expected['ai'] = 'Ultron';

        $this->assertEquals($expected, $data1->mergeRecursive($data2, $data3)->dump());

        $this->expectException(\InvalidArgumentException::class);

        $data1->mergeRecursive('', 123);
    }

    public function testFillKeys(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testDiff(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testMerge(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testFill(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testIntersectKey(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testIntersect(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testRange(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testFlip(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testDiffKeys(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testRand(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = arr([1, 2, 3]);
    }

    protected function tearDown(): void
    {
    }
}
