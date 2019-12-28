<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 .
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\DOM;

/**
 * The DOMTokenList class.
 *
 * @property-read string $value
 *
 * @since  3.5.3
 */
class DOMTokenList
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array|null
     */
    protected $supports;

    /**
     * Property html.
     *
     * @var HTMLElement
     */
    protected $html;

    /**
     * ClassList constructor.
     *
     * @param  HTMLElement  $html
     * @param  string       $name
     * @param  array|null   $supports
     */
    public function __construct(HTMLElement $html, string $name, ?array $supports = null)
    {
        $this->html = $html;
        $this->supports = $supports;
        $this->name = $name;
    }

    /**
     * add
     *
     * @param  string  ...$args
     *
     * @return  static
     *
     * @since  3.5.3
     */
    public function add(string ...$args): self
    {
        $classes = $this->getTokens();

        $classes = array_values(array_unique(array_merge($classes, $args)));

        $this->html->setAttribute($this->name, implode(' ', $classes));

        return $this;
    }

    /**
     * remove
     *
     * @param string ...$args
     *
     * @return  static
     *
     * @since  3.5.3
     */
    public function remove(string ...$args): self
    {
        $classes = $this->getTokens();

        $classes = array_diff($classes, $args);

        $this->html->setAttribute($this->name, implode(' ', $classes));

        return $this;
    }

    /**
     * item
     *
     * @param int $index
     *
     * @return  string|null
     *
     * @since  3.5.3
     */
    public function item(int $index): ?string
    {
        $classes = $this->getTokens();

        return $classes[$index] ?? null;
    }

    /**
     * toggle
     *
     * @param string    $class
     * @param bool|null $force
     *
     * @return  bool
     *
     * @since  3.5.3
     */
    public function toggle(string $class, ?bool $force = null): bool
    {
        if ($force === null) {
            $classes = $this->getTokens();

            if (in_array($class, $classes, true)) {
                $this->remove($class);

                return false;
            }

            $this->add($class);

            return true;
        }

        if ($force === true) {
            $this->add($class);

            return true;
        }

        $this->remove($class);

        return false;
    }

    /**
     * contains
     *
     * @param string $class
     *
     * @return  bool
     *
     * @since  3.5.3
     */
    public function contains(string $class): bool
    {
        return in_array($class, $this->getTokens(), true);
    }

    /**
     * getClasses
     *
     * @return  array
     *
     * @since  3.5.3
     */
    private function getTokens(): array
    {
        return array_filter(explode(' ', $this->html->getAttribute($this->name) ?? ''), 'strlen');
    }

    /**
     * Method to get property Html
     *
     * @return  HTMLElement
     *
     * @since  3.5.3
     */
    public function getHTMLElement(): HTMLElement
    {
        return $this->html;
    }

    /**
     * supports
     *
     * @param  string  $token
     *
     * @return  bool
     *
     * @throws \DOMException
     */
    public function supports(string $token): bool
    {
        if ($this->supports === null) {
            throw new \DOMException(
                'Failed to execute \'supports\' on \'DOMTokenList\': DOMTokenList has no supported tokens.'
            );
        }

        return in_array(strtolower($token), $this->supports, true);
    }

    /**
     * __isset
     *
     * @param  string  $name
     *
     * @return  bool
     */
    public function __isset(string $name)
    {
        if ($name === 'value') {
            return true;
        }

        return isset($this->$name);
    }

    /**
     * __get
     *
     * @param  string  $name
     *
     * @return  mixed
     */
    public function __get(string $name)
    {
        if ($name === 'value') {
            return $this->html->getAttribute($this->name);
        }

        return $this->$name;
    }
}
