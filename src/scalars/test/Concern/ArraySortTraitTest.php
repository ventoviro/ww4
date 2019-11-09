<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars\Test\Concern;

use PHPUnit\Framework\TestCase;
use Windwalker\Scalars\ArrayObject;
use Windwalker\Scalars\Concern\ArraySortTrait;
use Windwalker\Utilities\Str;
use function Windwalker\arr;

/**
 * The ArraySortTraitTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArraySortTraitTest extends TestCase
{
    protected ?ArrayObject $instance;

    public function testUksort(): void
    {
        $r = $this->instance->uksort(fn ($a, $b) => (int) $a > (int) $b);

        self::assertEquals(
            [
                1 => 'H',
                2 => 'Z',
                3 => 'A',
                5 => 'B',
            ],
            $r->dump()
        );
    }

    public function testRsort(): void
    {
        $r = $this->instance->rsort();

        self::assertEquals(
            ['Z', 'H', 'B', 'A'],
            $r->dump()
        );
    }

    /**
     * Test uasort
     *
     * @see  ArraySortTrait::uasort
     */
    public function testUasort(): void
    {
        $r = $this->instance->uasort(fn ($a, $b) => strcmp($a, $b));

        self::assertSame(
            [
                3 => 'A',
                5 => 'B',
                1 => 'H',
                2 => 'Z',
            ],
            $r->dump()
        );
    }

    /**
     * Test sortColumn
     *
     * @see  ArraySortTrait::sortColumn
     */
    public function testSortColumn(): void
    {
        $data = arr($src = [
            [
                'id' => 3,
                'title' => 'Othello',
                'data' => 123,
            ],
            [
                'id' => 2,
                'title' => 'Macbeth',
                'data' => [],
            ],
            [
                'id' => 4,
                'title' => 'Hamlet',
                'data' => true,
            ],
            [
                'id' => 1,
                'title' => 'Julius Caesar',
                'data' => (object) ['foo' => 'bar'],
            ],
        ]);

        // Keep key
        $a = $data->sortColumn('id');

        self::assertEquals(
            ['Julius Caesar', 'Macbeth', 'Othello', 'Hamlet'],
            $a->column('title')->dump()
        );

        self::assertEquals(
            [3, 1, 0, 2],
            $a->keys()->dump()
        );

        // No keep key
        $a = $data->sortColumn('id', false);

        self::assertEquals(
            ['Julius Caesar', 'Macbeth', 'Othello', 'Hamlet'],
            $a->column('title')->dump()
        );

        self::assertEquals(
            [0, 1, 2, 3],
            $a->keys()->dump()
        );
    }

    /**
     * Test asort
     *
     * @see  ArraySortTrait::asort
     */
    public function testAsort(): void
    {
        $a = $this->instance->asort();

        self::assertSame(
            [
                3 => 'A',
                5 => 'B',
                1 => 'H',
                2 => 'Z',
            ],
            $a->dump()
        );
    }

    /**
     * Test krsort
     *
     * @see  ArraySortTrait::krsort
     */
    public function testKrsort(): void
    {
        $a = $this->instance->krsort();

        self::assertSame(
            [
                5 => 'B',
                3 => 'A',
                2 => 'Z',
                1 => 'H',
            ],
            $a->dump()
        );
    }

    /**
     * Test natsort
     *
     * @see  ArraySortTrait::natsort
     */
    public function testNatsort(): void
    {
        $a = arr($src = ['img12.png', 'img10.png', 'img2.png', 'img1.png']);

        $a = $a->natsort();

        self::assertSame(
            [
                3 => 'img1.png',
                2 => 'img2.png',
                1 => 'img10.png',
                0 => 'img12.png',
            ],
            $a->dump()
        );
    }

    /**
     * Test ksort
     *
     * @see  ArraySortTrait::ksort
     */
    public function testKsort(): void
    {
        $a = $this->instance->ksort();

        self::assertSame(
            [
                1 => 'H',
                2 => 'Z',
                3 => 'A',
                5 => 'B',
            ],
            $a->dump()
        );
    }

    /**
     * Test sort
     *
     * @see  ArraySortTrait::sort
     */
    public function testSort(): void
    {
        $a = $this->instance->sort();

        self::assertSame(
            [
                0 => 'A',
                1 => 'B',
                2 => 'H',
                3 => 'Z',
            ],
            $a->dump()
        );
    }

    /**
     * Test natcasesort
     *
     * @see  ArraySortTrait::natcasesort
     */
    public function testNatcasesort(): void
    {
        $a = arr($src = ['IMG0.png', 'img12.png', 'img10.png', 'img2.png', 'img1.png', 'IMG3.png']);

        $a = $a->natcasesort();

        self::assertSame(
            [
                0 => 'IMG0.png',
                4 => 'img1.png',
                3 => 'img2.png',
                5 => 'IMG3.png',
                2 => 'img10.png',
                1 => 'img12.png',
            ],
            $a->dump()
        );
    }

    public function testArsort(): void
    {
        $a = $this->instance->arsort();

        self::assertSame(
            [
                2 => 'Z',
                1 => 'H',
                5 => 'B',
                3 => 'A',
            ],
            $a->dump()
        );
    }

    public function testUsort(): void
    {
        $r = $this->instance->usort(fn ($a, $b) => strcmp($a, $b));

        self::assertSame(
            [
                0 => 'A',
                1 => 'B',
                2 => 'H',
                3 => 'Z',
            ],
            $r->dump()
        );
    }

    protected function setUp(): void
    {
        $this->instance = arr([
            '3' => 'A',
            '2' => 'Z',
            '5' => 'B',
            '1' => 'H',
        ]);
    }

    protected function tearDown(): void
    {
    }
}
