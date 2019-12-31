<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\Query;

use Windwalker\Query\Clause\Clause;

/**
 * clause
 *
 * @param  string        $name
 * @param  array|string  $elements
 * @param  string        $glue
 *
 * @return  Clause
 */
function clause(string $name = '', $elements = [], string $glue = ' '): Clause
{
    return new Clause($name, $elements, $glue);
}
