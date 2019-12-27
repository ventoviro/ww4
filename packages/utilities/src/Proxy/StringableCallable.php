<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Proxy;

/**
 * The StringableCallable class.
 */
class StringableCallable extends CallableProxy
{
    /**
     * __toString
     *
     * @return  string
     */
    public function __toString()
    {
        return (string) $this();
    }
}
