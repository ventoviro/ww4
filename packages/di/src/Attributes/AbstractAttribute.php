<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DI\Attributes;

use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The AbstractAttribute class.
 */
class AbstractAttribute
{
    use OptionAccessTrait;

    /**
     * AbstractAttribute constructor.
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }
}
