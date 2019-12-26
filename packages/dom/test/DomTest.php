<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Dom\Test;

use Masterminds\HTML5;
use PHPUnit\Framework\TestCase;

/**
 * The DomTest class.
 */
class DomTest extends TestCase
{
    public function testDomElement()
    {
        $html = new HTML5();

        $impl = new \DOMImplementation();
        $dt = $impl->createDocumentType('html');

        $dom = $impl->createDocument('', '', $dt);

        $ele = new \DOMElement('img', '', 'qwe');
        $dom->appendChild($ele);
        $ele->setAttribute('data-foo', 'bar');

        show($ele->attributes, $dom->saveHTML($ele), $html->saveHTML($dom));
    }
}
