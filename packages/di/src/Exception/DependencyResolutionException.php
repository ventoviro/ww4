<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * The DependencyResolutionException class.
 */
class DependencyResolutionException extends \LogicException implements ContainerExceptionInterface
{
}
