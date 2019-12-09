<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Exception;

/**
 * The InvalidArgumentException class.
 */
class InvalidArgumentException extends \InvalidArgumentException implements
    CacheExceptionInterface,
    \Psr\Cache\InvalidArgumentException,
    \Psr\SimpleCache\InvalidArgumentException
{
    //
}
