<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Utilities\Assert;

/**
 * The ArgumentsAssert class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArgumentsAssert extends TypeAssert
{
    protected static string $exceptionClass = \InvalidArgumentException::class;
}
