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
 *
 * @property-read DOMTokenList $classList
 * @property-read DOMTokenList $relList
 * @property-read DOMStringMap $dataset
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

    /**
     * addClass
     *
     * @param string|callable $class
     *
     * @return  static
     *
     * @since  3.5.3
     */
    public function addClass(string $class)
    {
        $classes = array_filter(explode(' ', $class), 'strlen');

        $this->classList->add(...$classes);

        return $this;
    }

    /**
     * removeClass
     *
     * @param string|callable $class
     *
     * @return  static
     *
     * @since  3.5.3
     */
    public function removeClass(string $class)
    {
        $classes = array_filter(explode(' ', $class), 'strlen');

        $this->classList->remove(...$classes);

        return $this;
    }

    /**
     * toggleClass
     *
     * @param string    $class
     * @param bool|null $force
     *
     * @return  static
     *
     * @since  3.5.3
     */
    public function toggleClass(string $class, ?bool $force = null)
    {
        $this->classList->toggle($class, $force);

        return $this;
    }

    /**
     * hasClass
     *
     * @param string $class
     *
     * @return  static
     *
     * @since  3.5.3
     */
    public function hasClass(string $class): self
    {
        $this->classList->contains($class);

        return $this;
    }

    /**
     * data
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return  string|static
     *
     * @since  3.5.3
     */
    public function data(string $name, $value = null)
    {
        if ($value === null) {
            return $this->getAttribute('data-' . $name);
        }

        return $this->setAttribute('data-' . $name, $value);
    }

    /**
     * __get
     *
     * @param string $name
     *
     * @return  mixed
     *
     * @since  3.5.3
     */
    public function __get($name)
    {

        if ($name === 'dataset') {
            return new DOMStringMap($this);
        }

        if ($name === 'classList') {
            return new DOMTokenList($this, 'class');
        }

        if ($name === 'relList') {
            return new DOMTokenList($this, 'rel', [
                'alternate',
                'author',
                'dns-prefetch',
                'help',
                'icon',
                'license',
                'next',
                'pingback',
                'preconnect',
                'prefetch',
                'preload',
                'prerender',
                'prev',
                'search',
                'stylesheet'
            ]);
        }

        return $this->$name;
    }
}
