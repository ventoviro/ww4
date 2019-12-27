<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Dom;

/**
 * Html Elements collection.
 *
 * @since 2.0
 */
class DomCollection implements \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * Property elements.
     *
     * @var  DomElement[]|mixed[]
     */
    protected $elements = [];

    /**
     * Property strict.
     *
     * @var boolean
     */
    protected $strict = false;

    /**
     * Class init.
     *
     * @param array $elements
     */
    public function __construct($elements = [])
    {
        if ($elements instanceof static) {
            $elements = $elements->getElements();
        }

        $this->elements = (array) $elements;
    }

    /**
     * Convert all elements to string.
     *
     * @return  string
     */
    public function __toString()
    {
        $return = '';

        foreach ($this as $element) {
            $return .= (string) $element;
        }

        return $return;
    }

    /**
     * Retrieve an external iterator
     *
     * @return  \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * Whether a offset exists
     *
     * @param  mixed $offset An offset to check for.
     *
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->elements[$offset]);
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
        return $this->elements[$offset] ?? null;
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
        if ($offset === '' || $offset === null) {
            $this->elements[] = $value;

            return;
        }

        $this->elements[$offset] = $value;
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
        unset($this->elements[$offset]);
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * Method to get property Strict
     *
     * @return  boolean
     */
    public function getStrict()
    {
        return $this->strict;
    }

    /**
     * Method to set property strict
     *
     * @param   bool $strict
     *
     * @return  static  Return self to support chaining.
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;

        return $this;
    }

    /**
     * Method to get property Elements
     *
     * @return  \mixed[]|static[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Method to set property elements
     *
     * @param   \mixed[]|static[] $elements
     *
     * @return  static  Return self to support chaining.
     */
    public function setElements($elements)
    {
        if ($elements instanceof static) {
            $elements = $elements->getElements();
        }

        return $this;
    }
}
