<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Assert;

use InvalidArgumentException;

/**
 * The ArgumentsAssert class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArgumentsAssert extends TypeAssert
{
    protected static function exception(): callable
    {
        return fn (string $msg) => new InvalidArgumentException($msg);
    }
}
