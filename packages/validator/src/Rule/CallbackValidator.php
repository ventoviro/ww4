<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Validator\Rule;

use Windwalker\Validator\AbstractValidator;

/**
 * The CallbackValidator class.
 *
 * @since  3.2
 */
class CallbackValidator extends AbstractValidator
{
    /**
     * Property handler.
     *
     * @var  callable
     */
    protected $handler;

    /**
     * CallbackValidator constructor.
     *
     * @param  callable  $handler
     */
    public function __construct(callable $handler = null)
    {
        $this->handler = $handler;
    }

    /**
     * Test value and return boolean
     *
     * @param  mixed  $value
     *
     * @return  boolean
     */
    protected function doTest($value): bool
    {
        if (!$this->handler) {
            return true;
        }

        $handler = $this->handler;

        return $handler($value);
    }

    /**
     * Method to get property Handler
     *
     * @return  callable
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Method to set property handler
     *
     * @param  callable  $handler
     *
     * @return  static  Return self to support chaining.
     */
    public function setHandler(callable $handler)
    {
        $this->handler = $handler;

        return $this;
    }
}
