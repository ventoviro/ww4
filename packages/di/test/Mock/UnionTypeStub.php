<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Test\Mock;

use Windwalker\Scalars\ArrayObject;

/**
 * The UnionTypeStub class.
 */
class UnionTypeStub
{
    /**
     * UnionTypeStub constructor.
     */
    public function __construct(public \NonExistsClass|ArrayObject|\ArrayIterator $iter)
    {
    }
}
