<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session;

/**
 * Interface SessionInterface
 */
interface SessionInterface extends \Countable, \JsonSerializable
{
    public function set(string $key, $value): void;

    public function get(string $key, $default = null);

    public function remove(string $key): void;

    public function clear(): void;

    public function has(string $key): bool;

    public function hasChanged(): bool;

    public function count(): int;
}
