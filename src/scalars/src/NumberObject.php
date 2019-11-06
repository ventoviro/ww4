<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Windwalker\Utilities\Classes\StringableInterface;

/**
 * The BigNumber class.
 *
 * @since  __DEPLOY_VERSION__
 */
class NumberObject implements ScalarsInterface, StringableInterface
{
    protected BigNumber $number;

    /**
     * NumberObject constructor.
     *
     * @param  BigNumber|int|string  $number
     */
    public function __construct($number)
    {
        $this->number = BigDecimal::of($number);
    }

    public function toNumber(): NumberObject
    {
        return clone $this;
    }

    public function toString(): StringObject
    {
        return new StringObject((string) $this->toNumberValue());
    }

    public function toArray(): ArrayObject
    {
        return new ArrayObject([$this]);
    }

    /**
     * toNumberValue
     *
     * @return  float|int
     *
     * @since  __DEPLOY_VERSION__
     */
    public function toNumberValue()
    {
        return $this->number->hasNonZeroFractionalPart()
            ? $this->number->toFloat()
            : $this->number->toInt();
    }

    /**
     * Magic method to convert this object to string.
     *
     * @return  string
     */
    public function __toString(): string
    {
        return (string) $this->toNumberValue();
    }
}
