<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DOM;

use Windwalker\Utilities\Str;

/**
 * The HTMLElement class.
 */
abstract class HTMLElement
{
    /**
     * create
     *
     * @param  string  $name
     * @param  array   $attributes
     * @param  mixed   $content
     *
     * @return  DOMElement
     */
    public static function create(string $name, array $attributes = [], $content = null): DOMElement
    {
        return DOMElement::create($name, $attributes, $content)->asHTML();
    }

    /**
     * buildAttributes
     *
     * @param  array  $attributes
     *
     * @return  string
     */
    public static function buildAttributes(array $attributes): string
    {
        return DOMElement::buildAttributes($attributes, DOMElement::HTML);
    }
}
