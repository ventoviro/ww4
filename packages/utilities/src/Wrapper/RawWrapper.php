<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Wrapper;

/**
 * The RawWrapper class.
 */
class RawWrapper implements WrapperInterface, \Stringable
{
    /**
     * Property value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * RawWrapper constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * get
     *
     * @return  mixed
     *
     * @since  3.5.1
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * set
     *
     * @param  mixed  $value
     *
     * @return  static
     *
     * @since  3.5.1
     */
    public function set($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __invoke($src = null)
    {
        return $this->get();
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return (string) $this->get();
    }
}
