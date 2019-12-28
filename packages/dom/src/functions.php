<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\DOM;

use function Windwalker\value;

/**
 * html
 *
 * @param  DOMElement  $element
 *
 * @return  DOMElement
 */
function html(DOMElement $element): DOMElement
{
    return $element->asHTML();
}

/**
 * xml
 *
 * @param  DOMElement  $element
 *
 * @return  DOMElement
 */
function xml(DOMElement $element): DOMElement
{
    return $element->asXML();
}

/**
 * h
 *
 * @param  string  $name
 * @param  array   $attributes
 * @param  mixed   $content
 *
 * @return  DOMElement
 */
function h(string $name, array $attributes = [], $content = null): DOMElement
{
    return HTMLElement::create($name, $attributes, $content);
}

/**
 * div
 *
 * @param  array  $attributes
 * @param  mixed  $content
 *
 * @return  DOMElement
 */
function div(array $attributes = [], $content = null): DOMElement
{
    return h('div', $attributes, $content);
}

/**
 * span
 *
 * @param  array  $attributes
 * @param  mixed  $content
 *
 * @return  DOMElement
 */
function span(array $attributes = [], $content = null): DOMElement
{
    return h('span', $attributes, $content);
}

/**
 * span
 *
 * @param  mixed  $src
 * @param  array  $attributes
 *
 * @return  DOMElement
 */
function img($src, array $attributes = []): DOMElement
{
    $attributes['src'] = value($src);

    return h('img', $attributes);
}
