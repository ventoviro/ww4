<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Dom\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Dom\DomElement;

/**
 * The DomElementTest class.
 */
class DomElementTest extends AbstractDomTestCase
{
    /**
     * @var DomElement
     */
    protected $instance;

    /**
     * @see  DomElement::__construct
     */
    public function testConstruct(): void
    {
        $ele = DomElement::create(
            'field',
            [
                'name' => 'foo',
                'label' => 'FOO',
                'class' => function () {
                    return 'col-12 form-control';
                },
                'data-options' => [
                    'handle' => '.handle',
                    'enabled' => true
                ],
            ]
        );

        self::assertDomStringEqualsDomString(
            '<field name="foo" label="FOO" class="col-12 form-control" ' .
            'data-options="{&quot;handle&quot;:&quot;.handle&quot;,&quot;enabled&quot;:true}"/>',
            $ele
        );

        $ele = DomElement::create(
            'field',
            [
                'name' => 'foo',
                'label' => 'FOO',
                'class' => function () {
                    return 'col-12 form-control';
                },
                'data-options' => [
                    'handle' => '.handle',
                    'enabled' => true
                ],
            ],
            DomElement::create('span', [], 'Hello')
        );

        self::assertDomStringEqualsDomString(
            '<field name="foo" label="FOO" class="col-12 form-control" ' .
            'data-options="{&quot;handle&quot;:&quot;.handle&quot;,&quot;enabled&quot;:true}">' .
            '<span>Hello</span></field>',
            $ele
        );

        $attrs = $ele->attributes;
    }

    /**
     * @see  DomElement::getName
     */
    public function testGetName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  DomElement::offsetSet
     */
    public function testOffsetSet(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  DomElement::__toString
     */
    public function tesToString(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  DomElement::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  DomElement::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  DomElement::render
     */
    public function testRender(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  DomElement::getAttributes
     */
    public function testGetAttributes(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  DomElement::setName
     */
    public function testSetName(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  DomElement::setAttributes
     */
    public function testSetAttributes(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  DomElement::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = null;
    }

    protected function tearDown(): void
    {
    }
}
