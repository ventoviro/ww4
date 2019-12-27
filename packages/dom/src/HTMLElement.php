<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DOM;

use Masterminds\HTML5;

/**
 * The HtmlElement class.
 */
class HTMLElement extends DOMElement
{
    /**
     * @var string
     */
    protected static $factory = [HTMLFactory::class, 'element'];

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

        if (class_exists(HTML5::class)) {
            $html = HTMLFactory::html5()->saveHTML($this);
        } else {
            $html = $this->ownerDocument->saveHTML($this);
        }

        $this->ownerDocument->formatOutput = false;

        return $html;
    }
}
