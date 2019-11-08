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
 * The ArrayLoopTraitTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArrayLoopTraitTest extends TestCase
{
    protected ?ArrayObject $instance;

    public function testPartition(): void
    {
        [$a1, $a2] = $this->instance->partition(fn ($v) => $v > 1);

        self::assertEquals([2, 3], $a1->dump());
        self::assertEquals([1], $a2->dump());
    }

    public function testFindFirst(): void
    {
        $a = ArrayObject::range(1, 10);

        $r = $a->findFirst(fn ($v) => $v >= 5);

        self::assertEquals(5, $r);
    }

    public function testFilter(): void
    {
        $a = ArrayObject::range(1, 10);

        $a = $a->filter(fn ($v) => $v % 2 === 1);

        self::assertEquals([1, 3, 5, 7, 9], $a->values()->dump());
        self::assertEquals([0, 2, 4, 6, 8], $a->keys()->dump());
    }

    public function testWalkRecursive(): void
    {
        $a = arr($src = [
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
        ]);

        $callback = fn (&$v, $k) => $v = strtoupper($v);
        $b = $a->walkRecursive($callback);
        array_walk_recursive($src, $callback);

        self::assertEquals($src, $b->dump());
    }

    public function testQuery(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testMap(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testWalk(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testFind(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testReduce(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testMapWithKeys(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testEach(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testMapAs(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testReject(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    public function testMapRecursive(): void
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
