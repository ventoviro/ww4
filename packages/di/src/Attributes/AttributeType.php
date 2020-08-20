<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Attributes;

/**
 * The AttributeType class.
 */
abstract class AttributeType
{
    public const FUNCTION_METHOD = 'function_method';

    public const PROPERTIES = 'properties';

    public const CLASSES = 'classes';

    public const PARAMETERS = 'parameters';
}
