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
 * The HtmlFactory class.
 */
class HtmlFactory extends DomFactory
{
    /**
     * element
     *
     * @param  string  $name
     * @param  null    $value
     *
     * @return  HtmlElement
     */
    public static function element(string $name, $value = null)
    {
        return parent::element($name, $value);
    }

    /**
     * create
     *
     * @param  array  $options
     *
     * @return  \DOMDocument
     */
    public static function create(array $options = []): \DOMDocument
    {
        $impl = new \DOMImplementation();

        $dt = $impl->createDocumentType('html');

        $dom = $impl->createDocument('', '', $dt);
        $dom->registerNodeClass(\DOMElement::class, HtmlElement::class);

        $dom->encoding = $options['encoding'] ?? 'UTF-8';

        return $dom;
    }
}
