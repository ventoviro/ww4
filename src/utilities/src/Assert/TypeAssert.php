<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Utilities\Assert;

/**
 * The Assert class.
 *
 * @since  __DEPLOY_VERSION__
 */
class TypeAssert
{
    protected static string $exceptionClass = \AssertionError::class;

    public static function assert($assertion, string $message, $value = null, ?string $caller = null): void
    {
        if (is_callable($assertion)) {
            $result = $assertion();
        } else {
            $result = (bool) $assertion;
        }

        if (!$result) {
            $caller ??= static::getCaller();

            static::throwException(static::$exceptionClass, $message, $value, $caller);
        }
    }

    public static function invalidArguments(string $message, $value = null, ?string $caller = null): void
    {
        $caller ??= static::getCaller();

        static::throwException(\InvalidArgumentException::class, $message, $value, $caller);
    }

    public static function throwException(string $class, string $message, $value = null, ?string $caller = null): void
    {
        $caller ??= static::getCaller();

        throw new $class(sprintf($message, $caller, static::describeValue($value)));
    }

    public static function getCaller(int $backSteps = 2): string
    {
        $trace = debug_backtrace()[$backSteps];

        return trim(($trace['class'] ?? '') . '::' . ($trace['function']), ':') . '()';
    }

    public static function describeValue($value): string
    {
        if ($value === null) {
            return '(NULL)';
        }

        if ($value === true) {
            return 'BOOL (TRUE)';
        }

        if ($value === false) {
            return 'BOOL (FALSE)';
        }

        if (is_object($value)) {
            return get_class($value);
        }

        if (is_array($value)) {
            return 'array';
        }

        if (is_string($value)) {
            return sprintf('string(%s) "%s"', strlen($value), $value);
        }

        if (is_numeric($value)) {
            return sprintf('%s(%s)', gettype($value), $value);
        }

        return (string) $value;
    }
}
