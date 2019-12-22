<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Crypt;

/**
 * The Key class.
 */
class Key extends HiddenString
{
    /**
     * Key is disallow to print as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return '';
    }
}
