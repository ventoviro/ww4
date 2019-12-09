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
 * The CacheExceptionInterface class.
 */
interface CacheExceptionInterface extends
    \Psr\Cache\CacheException,
    \Psr\SimpleCache\CacheException
{
}
