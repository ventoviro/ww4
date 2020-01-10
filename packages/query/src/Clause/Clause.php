<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Query\Clause;

use org\bovigo\vfs\vfsStream;

/**
 * Query Clause Class.
 *
 * @property-read  string $name      The name of the element.
 * @property-read  array  $elements  An array of elements.
 * @property-read  string $glue      Glue piece.
 *
 * @since  2.0
 */
class Clause implements \Countable, ClauseInterface
{
    /**
     * @var    string  The name of the element.
     * @since  2.0
     */
    protected $name;

    /**
     * @var    array  An array of elements.
     * @since  2.0
     */
    protected $elements = [];

    /**
     * @var    string  Glue piece.
     * @since  2.0
     */
    protected $glue;

    /**
     * Constructor.
     *
     * @param   string $name     The name of the clause.
     * @param   mixed  $elements String or array.
     * @param   string $glue     The glue for elements.
     *
     * @since   2.0
     */
    public function __construct(string $name = '', $elements = [], string $glue = ' ')
    {
        $this->name = $name;
        $this->glue = $glue;

        $this->append($elements);
    }

    /**
     * Magic function to convert the query element to a string.
     *
     * @return  string
     *
     * @since   2.0
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * toString
     *
     * @return  string
     */
    public function render()
    {
        if (substr($this->name, -2) === '()') {
            return substr($this->name, 0, -2) . '(' . implode($this->glue, $this->elements) . ')';
        }

        return ltrim($this->name . ' ', ' ') . implode($this->glue, $this->elements);
    }

    /**
     * Appends element parts to the internal list.
     *
     * @param   mixed $elements String or array.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function append($elements): void
    {
        if (is_array($elements)) {
            $this->elements = array_merge($this->elements, $elements);
        } else {
            $this->elements = array_merge($this->elements, [$elements]);
        }
    }

    /**
     * prepend
     *
     * @param   mixed $elements String or array.
     *
     * @return  void
     */
    public function prepend($elements): void
    {
        if (!is_array($elements)) {
            $elements = [$elements];
        }

        array_unshift($this->elements, ...$elements);
    }

    /**
     * Gets the elements of this element.
     *
     * @return  array
     *
     * @since   2.0
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Method to provide deep copy support to nested objects and arrays
     * when cloning.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function __clone()
    {
        foreach (get_object_vars($this) as $k => $v) {
            if (is_object($v) || is_array($v)) {
                $this->{$k} = unserialize(serialize($v));
            }
        }
    }

    /**
     * Method to get property Glue
     *
     * @return  string
     */
    public function getGlue(): string
    {
        return $this->glue;
    }

    /**
     * Method to set property glue
     *
     * @param   string $glue
     *
     * @return  static  Return self to support chaining.
     */
    public function setGlue($glue)
    {
        $this->glue = $glue;

        return $this;
    }

    /**
     * Method to get property Name
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Method to set property name
     *
     * @param   string $name
     *
     * @return  static  Return self to support chaining.
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * Method to set property elements
     *
     * @param  array|string  $elements
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setElements($elements)
    {
        $this->elements = [];

        $this->append($elements);

        return $this;
    }

    public function __isset(string $name): bool
    {
        return isset($this->$name);
    }

    public function __get(string $name)
    {
        return $this->$name;
    }
}
