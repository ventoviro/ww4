<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker;

use Windwalker\Validator\Rule\RegexValidator;

/**
 * regex
 *
 * @param  string  $pattern
 * @param  string  $modifiers
 *
 * @return  RegexValidator
 */
function regex(string $pattern, string $modifiers = ''): RegexValidator
{
    return new RegexValidator($pattern, $modifiers);
}
