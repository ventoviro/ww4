<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Definition;

use Windwalker\DI\Exception\DefinitionResolveException;

/**
 * The ProtectedDefinition class.
 */
class ProtectedDefinition extends DecoratorDefinition
{
    /**
     * Set new value or factory callback to this definition.
     *
     * @param  mixed  $value  Value or callable.
     *
     * @return  void
     */
    public function set($value): void
    {
        throw new DefinitionResolveException('This Value / Definition is protected.');
    }
}
