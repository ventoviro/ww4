<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Test\Iterator;

use PHPUnit\Framework\TestCase;
use Windwalker\Utilities\Iterator\NestedIterator;

/**
 * The MultiLevelIteratorTest class.
 */
class NestedIteratorTest extends TestCase
{
    /**
     * @var NestedIterator
     */
    protected $instance;

    /**
     * @see  NestedIterator::__construct
     */
    public function testNestedWrap(): void
    {
        $iter = new NestedIterator(['a', 'b', 'c', 'd', 'e', 'f']);
        $iter = $iter->wrap(
            static function ($iterator) {
                foreach ($iterator as $item) {
                    yield strtoupper($item);
                }
            }
        )
            ->wrap(
                static function ($iterator) {
                    foreach ($iterator as $item) {
                        if ($item !== 'D') {
                            yield $item;
                        }
                    }
                }
            );

        self::assertEquals(
            ['A', 'B', 'C', 'E', 'F'],
            iterator_to_array($iter)
        );
    }

    public function testRewind()
    {
        $iter = new NestedIterator(['a', 'b', 'c', 'd', 'e', 'f']);
        $iter = $iter->wrap(
            static function ($iterator) {
                foreach ($iterator as $item) {
                    yield strtoupper($item);
                }
            }
        )
            ->wrap(
                static function ($iterator) {
                    foreach ($iterator as $item) {
                        if ($item !== 'D') {
                            yield $item;
                        }
                    }
                }
            );

        iterator_to_array($iter);

        self::assertNull($iter->current());

        $iter->rewind();

        self::assertEquals('A', $iter->current());
    }

    public function testRewindGenerator(): void
    {
        $gen = function () {
            foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $item) {
                yield $item;
            }
        };

        $iter = new NestedIterator($gen());
        $iter = $iter->wrap(
            static function ($iterator) {
                foreach ($iterator as $item) {
                    yield strtoupper($item);
                }
            }
        );

        iterator_to_array($iter);

        self::assertNull($iter->current());

        $iter->rewind();

        $this->expectExceptionMessage('Cannot traverse an already closed generator');

        self::assertEquals('A', $iter->current());
    }

    public function testRewindCallback(): void
    {
        $gen = function () {
            foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $item) {
                yield $item;
            }
        };

        $iter = new NestedIterator($gen);
        $iter = $iter->wrap(
            static function ($iterator) {
                foreach ($iterator as $item) {
                    yield strtoupper($item);
                }
            }
        );

        iterator_to_array($iter);

        self::assertNull($iter->current());

        $iter->rewind();

        self::assertEquals('A', $iter->current());
    }

    protected function setUp(): void
    {
        $this->instance = null;
    }

    protected function tearDown(): void
    {
    }
}
