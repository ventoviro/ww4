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
 * Interface DirectivesExtensionInterface
 */
interface DirectivesExtensionInterface extends EdgeExtensionInterface
{
    /**
     * getDirectives
     *
     * @return  callable[]
     */
    public function getDirectives(): array;
}
