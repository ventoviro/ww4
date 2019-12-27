<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Dom;

/**
 * The HtmlElement class.
 */
class HtmlElement extends DomElement
{
    /**
     * @var string
     */
    protected static $factory = [HtmlFactory::class, 'element'];

    /**
     * render
     *
     * @param  bool  $format
     *
     * @return  string
     */
    public function render(bool $format = false): string
    {
        $this->ownerDocument->formatOutput = $format;

        $html = HtmlFactory::html5()->saveHTML($this);

        $this->ownerDocument->formatOutput = false;

        return $html;
    }
}
