<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\Dom;

use function Windwalker\value;

/**
 * h
 *
 * @param  string  $name
 * @param  array   $attributes
 * @param  mixed   $content
 *
 * @return  callable
 */
function h(string $name, array $attributes = [], $content = null): callable
{
    return static function () use ($name, $attributes, $content): DomElement {
        return DomElement::create($name, $attributes, value($content));
    };
}

/**
 * div
 *
 * @param  array   $attributes
 * @param  mixed   $content
 *
 * @return  callable
 */
function div(array $attributes = [], $content = null): callable
{
    return h('div', $attributes, $content);
}

/**
 * span
 *
 * @param  array   $attributes
 * @param  mixed   $content
 *
 * @return  callable
 */
function span(array $attributes = [], $content = null): callable
{
    return h('span', $attributes, $content);
}

/**
 * span
 *
 * @param  mixed  $src
 * @param  array  $attributes
 *
 * @return  callable
 */
function img($src, array $attributes = []): callable
{
    $attributes['src'] = value($src);

    return h('img', $attributes);
}
