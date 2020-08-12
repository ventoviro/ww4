<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Cookie;

/**
 * Interface CookiesInterface
 */
interface CookiesInterface
{
    public function set(string $name, string $value): bool;

    public function get(string $name): ?string;
}
