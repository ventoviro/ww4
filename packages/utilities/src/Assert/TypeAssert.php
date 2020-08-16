<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Assert;

use TypeError;

/**
 * The Assert class.
 *
 * @since  __DEPLOY_VERSION__
 */
class TypeAssert
{
    /**
     * assert
     *
     * @param  bool|callable  $assertion
     * @param  string         $message
     * @param  mixed          $value
     * @param  callable|null  $exception
     *
     * @return  void
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function assert(bool|callable $assertion, string $message, $value = null, ?callable $exception = null): void
    {
        if (is_callable($assertion)) {
            $result = $assertion();
        } else {
            $result = (bool) $assertion;
        }

        if (!$result) {
            static::createAssert($exception)->throwException($message, $value);
        }
    }

    public static function createAssert(?callable $exception = null): Assert
    {
        return new Assert($exception ?? static::exception(), Assert::getCaller(2));
    }

    protected static function exception(): callable
    {
        return fn (string $msg) => new TypeError($msg);
    }

    public static function describeValue($value): string
    {
        return Assert::describeValue($value);
    }
}
