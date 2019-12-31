<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Test\Mock;

/**
 * The MockConnection class.
 */
class MockConnection
{
    public function quote(string $value): string
    {
        $value = addslashes($value);

        return "'$value'";
    }
}
