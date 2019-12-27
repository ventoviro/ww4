<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Dom;

use Symfony\Component\DomCrawler\Crawler;
use Windwalker\Utilities\Wrapper\RawWrapper;
use Windwalker\Utilities\Wrapper\WrapperInterface;
use function Windwalker\value;

/**
 * Class DomElement
 *
 * @since 2.0
 */
class DomElement extends \DOMElement implements \ArrayAccess
{
    /**
     * @var string
     */
    protected static $factory = [DomFactory::class, 'element'];

    /**
     * create
     *
     * @param  string  $name
     * @param  array   $attributes
     * @param  mixed   $content
     *
     * @return  DomElement
     */
    public static function create(string $name, array $attributes = [], $content = null)
    {
        /** @var static $ele */
        $ele = (static::$factory)($name);

        $ele->setAttributes($attributes);

        if ($content !== null) {
            static::insertContentTo($content, $ele);
        }

        return $ele;
    }

    /**
     * valueToString
     *
     * @param mixed $value
     *
     * @return  string
     */
    protected static function valueToString($value): string
    {
        $value = value($value);

        if (is_stringable($value)) {
            return (string) $value;
        }

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        return $value;
    }

    /**
     * insertContentTo
     *
     * @param  mixed     $content
     * @param  \DOMNode  $node
     *
     * @return  void
     */
    protected static function insertContentTo($content, \DOMNode $node): void
    {
        $content = value($content);

        if (is_array($content)) {
            $fragment = $node->ownerDocument->createDocumentFragment();

            foreach ($content as $key => $c) {
                static::insertContentTo($c, $fragment);
            }

            $node->appendChild($fragment);

            return;
        }

        if ($content instanceof \DOMNode) {
            $node->appendChild($content);
            return;
        }

        $text = $node->ownerDocument->createTextNode((string) $content);

        $node->appendChild($text);
    }

    /**
     * Adds new child at the end of the children
     * @link  https://php.net/manual/en/domnode.appendchild.php
     *
     * @param  \DOMNode  $newnode  The appended child.
     *
     * @return \DOMNode The node added.
     */
    public function appendChild(\DOMNode $newnode): \DOMNode
    {
        if (!$this->ownerDocument->isSameNode($newnode->ownerDocument)) {
            $newnode = $this->ownerDocument->importNode($newnode->cloneNode(true), true);
        }

        return parent::appendChild($newnode);
    }

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

        $xml = $this->ownerDocument->saveXML($this);

        $this->ownerDocument->formatOutput = false;

        return $xml;
    }

    /**
     * Convert this object to string.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * getAttributes
     *
     * @return  array
     */
    public function getAttributes(): array
    {
        if ($this->attributes === null) {
            return [];
        }

        return iterator_to_array($this->attributes);
    }

    /**
     * Set all attributes.
     *
     * @param   array $attribs All attributes.
     *
     * @return  static  Return self to support chaining.
     */
    public function setAttributes(array $attribs)
    {
        foreach ($attribs as $key => $attribute) {
            $this->setAttribute($key, static::valueToString($attribute));
        }

        return $this;
    }

    /**
     * querySelectorAll
     *
     * @param  string  $selector
     *
     * @return  Crawler
     */
    public function querySelectorAll(string $selector): Crawler
    {
        return $this->getCrawler()->filter($selector);
    }

    /**
     * querySelector
     *
     * @param  string  $selector
     *
     * @return  Crawler
     */
    public function querySelector(string $selector): Crawler
    {
        return $this->getCrawler()->filter($selector)->first();
    }

    /**
     * getCrawler
     *
     * @return  Crawler
     */
    public function getCrawler(): Crawler
    {
        if (!class_exists(Crawler::class)) {
            throw new \LogicException('Please install symfony/dom-crawler first.');
        }

        return new Crawler($this);
    }

    /**
     * Get element tag name.
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->tagName;
    }

    /**
     * Set element tag name.
     *
     * @param   string $name Set element tag name.
     *
     * @return  static  Return self to support chaining.
     */
    public function setName(string $name)
    {
        $this->tagName = $name;

        return $this;
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for.
     *
     * @return boolean True on success or false on failure.
     *                 The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->hasAttribute($offset);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * Offset to set
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->removeAttribute($offset);
    }
}
