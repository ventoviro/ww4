<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\DOM;

/**
 * The HtmlFactory class.
 */
class HTMLFactory extends DOMFactory
{
    /**
     * element
     *
     * @param  string  $name
     * @param  null    $value
     *
     * @return  HTMLElement
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
        $dom->registerNodeClass(\DOMElement::class, HTMLElement::class);

        $dom->encoding = $options['encoding'] ?? 'UTF-8';

        return $dom;
    }
}
