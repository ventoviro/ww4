<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session;

use Windwalker\Utilities\Contract\SimpleAccessibleInterface;

/**
 * Interface SessionInterface
 */
interface SessionInterface extends \Countable, \JsonSerializable, SimpleAccessibleInterface
{
    public function clear(): bool;
}
