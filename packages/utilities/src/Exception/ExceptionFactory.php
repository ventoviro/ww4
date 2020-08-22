<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Exception;

use Windwalker\Utilities\Assert\Assert;

/**
 * The ExceptionFactory class.
 */
class ExceptionFactory
{
    /**
     * badMethodCall
     *
     * @param  string       $name
     * @param  string|null  $caller
     *
     * @return \BadMethodCallException
     */
    public static function badMethodCall(string $name, ?string $caller = null): \BadMethodCallException
    {
        return new \BadMethodCallException(
            sprintf(
                'Call to undefined method: %s::%s()',
                $caller ?? Assert::getCaller(2),
                $name
            )
        );
    }
}
