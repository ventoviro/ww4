<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

declare(strict_types=1);

namespace Windwalker\Test\Traits;

use Throwable;
use Windwalker\Test\Helper\TestStringHelper;

/**
 * StringTestTrait
 *
 * @since  {DEPLOY_VERSION}
 */
trait BaseAssertionTrait
{
    /**
     * assertStringDataEquals
     *
     * @param  string  $expected
     * @param  string  $actual
     * @param  string  $message
     *
     * @return  void
     */
    public static function assertStringDataEquals(
        $expected,
        $actual,
        string $message = ''
    ): void {
        static::assertEquals(
            TestStringHelper::clean($expected),
            TestStringHelper::clean($actual),
            $message
        );
    }

    /**
     * assertStringDataEquals
     *
     * @param  string  $expected
     * @param  string  $actual
     * @param  string  $message
     *
     * @return  void
     */
    public static function assertStringSafeEquals(
        $expected,
        $actual,
        string $message = ''
    ): void {
        static::assertEquals(
            trim(TestStringHelper::removeCRLF($expected)),
            trim(TestStringHelper::removeCRLF($actual)),
            $message
        );
    }

    /**
     * assertExpectedException
     *
     * @param  callable  $closure
     * @param  string    $class
     * @param  string    $msg
     * @param  int       $code
     * @param  string    $message
     *
     * @return  void
     */
    public static function assertExpectedException(
        callable $closure,
        $class = Throwable::class,
        $msg = null,
        $code = null,
        $message = ''
    ): void {
        if (is_object($class)) {
            $class = get_class($class);
        }

        try {
            $closure();
        } catch (Throwable $t) {
            static::assertInstanceOf($class, $t, $message);

            if ($msg !== null) {
                static::assertStringStartsWith($msg, $t->getMessage(), $message);
            }

            if ($code !== null) {
                static::assertEquals($code, $t->getCode(), $message);
            }

            return;
        }

        static::fail('No exception or throwable caught.');
    }

    /**
     * Asserts that two associative arrays are similar.
     *
     * Both arrays must have the same indexes with identical values
     * without respect to key ordering
     *
     * @param array $expected
     * @param array $array
     */
    public static function assertArraySimilar(array $expected, array $array): void
    {
        static::assertEquals([], array_diff_key($array, $expected));

        foreach ($expected as $key => $value) {
            if (is_array($value)) {
                static::assertArraySimilar($value, $array[$key]);
            } else {
                static::assertContains($value, $array);
            }
        }
    }
}
