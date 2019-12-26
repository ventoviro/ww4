<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Dom\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Dom\Format\DomFormatter;
use Windwalker\Dom\Format\HtmlFormatter;
use Windwalker\Test\Helper\TestDomHelper;
use Windwalker\Test\Traits\BaseAssertionTrait;

/**
 * The DomTestCase class.
 *
 * @since  2.0
 */
class AbstractDomTestCase extends TestCase
{
    use BaseAssertionTrait;

    /**
     * Asserts that two variables are equal.
     *
     * @param  mixed   $expected
     * @param  mixed   $actual
     * @param  string  $message
     */
    public static function assertDomStringEqualsDomString(
        $expected,
        $actual,
        $message = ''
    ): void {
        self::assertEquals(
            TestDomHelper::minify((string) $expected),
            TestDomHelper::minify((string) $actual),
            $message
        );
    }

    /**
     * Asserts that two variables are equal.
     *
     * @param  mixed   $expected
     * @param  mixed   $actual
     * @param  string  $message
     */
    public static function assertDomFormatEquals(
        $expected,
        $actual,
        $message = ''
    ): void {
        self::assertEquals(
            DomFormatter::format((string) $expected),
            DomFormatter::format((string) $actual),
            $message
        );
    }

    /**
     * Asserts that two variables are equal.
     *
     * @param mixed   $expected
     * @param mixed   $actual
     * @param string  $message
     *
     * @throws \Exception
     */
    public function assertHtmlFormatEquals(
        $expected,
        $actual,
        $message = ''
    ): void {
        $this->assertEquals(
            HtmlFormatter::format((string) $expected),
            HtmlFormatter::format((string) $actual),
            $message
        );
    }
}
