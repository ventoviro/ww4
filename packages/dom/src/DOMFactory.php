<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DOM;

use DOMComment;
use DOMDocument;
use DOMDocumentFragment;
use DOMElement as NativeDOMElement;
use DOMImplementation;
use DOMText;
use Masterminds\HTML5;

/**
 * The DomTree class.
 */
class DOMFactory
{
    /**
     * @var DOMDocument
     */
    protected static $dom;

    /**
     * @var HTML5
     */
    protected static $html5;

    /**
     * getInstance
     *
     * @param  DOMDocument|null  $dom
     *
     * @return  DOMDocument
     */
    public static function document(?DOMDocument $dom = null): DOMDocument
    {
        if ($dom) {
            static::$dom = $dom;
        }

        if (!static::$dom) {
            static::$dom = static::create();
        }

        return static::$dom;
    }

    /**
     * html5
     *
     * @param  HTML5|null  $html5
     *
     * @return  HTML5
     */
    public static function html5(?HTML5 $html5 = null): HTML5
    {
        if ($html5) {
            static::$html5 = $html5;
        }

        if (!static::$html5) {
            static::$html5 = new HTML5(['disable_html_ns' => true]);
        }

        return static::$html5;
    }

    /**
     * fragment
     *
     * @return  DOMDocumentFragment
     */
    public static function fragment(): DOMDocumentFragment
    {
        return static::document()->createDocumentFragment();
    }

    /**
     * element
     *
     * @param  string  $name
     * @param  null    $value
     *
     * @return  DOMElement
     */
    public static function element(string $name, $value = null)
    {
        if ($value !== null) {
            return static::document()->createElement($name, $value);
        }

        return static::document()->createElement($name);
    }

    /**
     * comment
     *
     * @param  string  $data
     *
     * @return  DOMComment
     */
    public static function comment(string $data): DOMComment
    {
        return static::document()->createComment($data);
    }

    /**
     * textNode
     *
     * @param  string  $content
     *
     * @return  DOMText
     */
    public static function textNode(string $content): DOMText
    {
        return static::document()->createTextNode($content);
    }

    /**
     * create
     *
     * @param  array  $options
     *
     * @return  DOMDocument
     */
    public static function create(array $options = []): DOMDocument
    {
        $impl = new DOMImplementation();

        $dom = $impl->createDocument();
        $dom->registerNodeClass(NativeDOMElement::class, DOMElement::class);

        $dom->encoding = $options['encoding'] ?? 'UTF-8';

        return $dom;
    }
}
