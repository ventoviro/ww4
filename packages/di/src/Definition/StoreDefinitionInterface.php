<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Definition;

/**
 * Interface StoreDefinitionInterface
 */
interface StoreDefinitionInterface extends DefinitionInterface
{
    public function isShared(): bool;

    public function isProtected(): bool;

    public function extend(\Closure $closure);

    public function reset(): void;
}
