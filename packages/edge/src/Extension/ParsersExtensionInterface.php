<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Edge\Extension;

/**
 * Interface ParsersExtensionInterface
 */
interface ParsersExtensionInterface extends EdgeExtensionInterface
{
    /**
     * getParsers
     *
     * @return  callable[]
     */
    public function getParsers(): array;
}
