<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Validator;

/**
 * The AbstractValidator class.
 *
 * @since  2.0
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * Property error.
     *
     * @var string
     */
    protected $error = '';

    /**
     * Property message.
     *
     * @var string
     */
    protected $message = '';

    /**
     * Property multiple.
     *
     * @var  boolean
     */
    protected $multiple = false;

    /**
     * Validate this value and set error message..
     *
     * @param  mixed  $value
     *
     * @return  boolean
     */
    public function test($value): bool
    {
        if (!$this->doTest($value)) {
            // TODO: Use exception after 4.0
            $this->setError($this->formatMessage($this->getMessage(), $value));

            return false;
        }

        return true;
    }

    /**
     * Just invoke this object to test value.
     *
     * @param  mixed  $value
     *
     * @return  bool
     */
    public function __invoke($value): bool
    {
        return $this->test($value);
    }

    /**
     * Test value and return boolean
     *
     * @param  mixed  $value
     *
     * @return  boolean
     */
    abstract protected function doTest($value): bool;

    /**
     * Get error message.
     *
     * @return  string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Method to set property error
     *
     * @param  string  $error
     *
     * @return  static  Return self to support chaining.
     */
    public function setError($error = null)
    {
        $this->error = $error ?: $this->getMessage();

        return $this;
    }

    /**
     * Set error message.
     *
     * @param  string  $message
     *
     * @return  static
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Method to get property Message
     *
     * @return  string
     */
    protected function getMessage()
    {
        return $this->message;
    }

    /**
     * formatMessage
     *
     * @param  string  $message
     * @param  mixed   $value
     *
     * @return string
     */
    protected function formatMessage($message, $value)
    {
        return $message;
    }
}
