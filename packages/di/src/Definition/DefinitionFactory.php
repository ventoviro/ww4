<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Definition;

/**
 * The DefinitionFactory class.
 */
class DefinitionFactory
{
    public static function create($value): DefinitionInterface
    {
        if ($value instanceof DefinitionInterface) {
            return $value;
        }

        if ($value instanceof \Closure) {
            $value = fn () => $value;
        }

        return new ClosureDefinition($value);
    }

    public static function wrap($value)
    {
        if ($value instanceof DefinitionInterface) {
            return $value;
        }

        return new ValueDefinition($value);
    }
}
