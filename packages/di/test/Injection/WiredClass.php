<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Test\Injection;

use Windwalker\DI\Attributes\Autowire;
use Windwalker\Scalars\StringObject;

/**
 * The WiredClass class.
 */
@@Autowire
class WiredClass
{
    public array $logs = [];

    /**
     * WiredClass constructor.
     *
     * @param  array              $logs
     * @param  StringObject|null  $foo
     */
    public function __construct(array $logs = [], ?StringObject $foo = null)
    {
        $this->logs[] = $foo;
    }
}
