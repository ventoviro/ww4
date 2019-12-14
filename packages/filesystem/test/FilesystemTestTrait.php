<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Filesystem\Test;

use Windwalker\Filesystem\Path\PathLocator;

/**
 * Trait FilesystemTestTrait
 */
trait FilesystemTestTrait
{
    /**
     * assertPathEquals
     *
     * @param  mixed   $expect
     * @param  mixed   $actual
     * @param  string  $message
     *
     * @return  mixed
     */
    public static function assertPathEquals($expect, $actual, string $message = '')
    {
        return self::assertEquals(
            PathLocator::clean($expect),
            PathLocator::clean($actual),
            $message
        );
    }

    /**
     * assertPathEquals
     *
     * @param  mixed   $expect
     * @param  mixed   $actual
     * @param  string  $message
     *
     * @return  mixed
     */
    public static function assertRealpathEquals($expect, $actual, string $message = '')
    {
        return self::assertEquals(
            PathLocator::normalize($expect),
            PathLocator::normalize($actual),
            $message
        );
    }
}
