<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars\Test\Concern;

use PHPUnit\Framework\TestCase;
use Windwalker\Scalars\Concern\StringInflectorTrait;
use Windwalker\Scalars\StringObject;
use Windwalker\Utilities\StrInflector;
use function Windwalker\str;

/**
 * The StringInflectorTraitTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class StringInflectorTraitTest extends TestCase
{
    protected ?StringObject $instance;

    /**
     * Test isSingular
     *
     * @see       StringInflectorTrait::isSingular
     *
     * @dataProvider \Windwalker\Utilities\Test\StrInflectorTest::providerSinglePlural
     *
     * @param  string  $singular
     * @param  string  $plural
     */
    public function testIsSingular(string $singular, string $plural): void
    {
        self::assertSame(
            StrInflector::isSingular($singular),
            str($singular)->isSingular()
        );
        self::assertSame(
            StrInflector::isSingular($plural),
            str($plural)->isSingular()
        );
    }

    /**
     * Test isPlural
     *
     * @see  StringInflectorTrait::isPlural
     *
     * @dataProvider \Windwalker\Utilities\Test\StrInflectorTest::providerSinglePlural
     *
     * @param  string  $singular
     * @param  string  $plural
     */
    public function testIsPlural(string $singular, string $plural): void
    {
        self::assertSame(
            StrInflector::isPlural($singular),
            str($singular)->isPlural()
        );
        self::assertSame(
            StrInflector::isPlural($plural),
            str($plural)->isPlural()
        );
    }

    /**
     * Test toPlural
     *
     * @see  StringInflectorTrait::toPlural
     *
     * @dataProvider \Windwalker\Utilities\Test\StrInflectorTest::providerSinglePlural
     *
     * @param  string  $singular
     * @param  string  $plural
     */
    public function testToPlural(string $singular, string $plural): void
    {
        self::assertEquals(
            $plural,
            (string) str($singular)->toPlural()
        );
    }

    /**
     * Test toSingular
     *
     * @see  StringInflectorTrait::toSingular
     *
     * @dataProvider \Windwalker\Utilities\Test\StrInflectorTest::providerSinglePlural
     *
     * @param  string  $singular
     * @param  string  $plural
     */
    public function testToSingular(string $singular, string $plural): void
    {
        self::assertEquals(
            $singular,
            (string) str($plural)->toSingular()
        );
    }

    protected function setUp(): void
    {
        $this->instance = null;
    }

    protected function tearDown(): void
    {
    }
}
