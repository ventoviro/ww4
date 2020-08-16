<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Assert;

/**
 * The LogicAssert class.
 */
class LogicAssert extends TypeAssert
{
    protected static function exception(): callable
    {
        return fn (string $message) => new \LogicException($message);
    }
}
