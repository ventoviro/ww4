<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Iterator;

use Iterator;
use Windwalker\Utilities\TypeCast;

/**
 * The UniqueIterator class.
 */
class UniqueIterator extends \FilterIterator
{
    /**
     * @var array
     */
    protected $exists = [];

    /**
     * @var int
     */
    protected $flags = SORT_STRING;

    /**
     * @inheritDoc
     */
    public function __construct(Iterator $iterator, int $flags = SORT_STRING)
    {
        parent::__construct($iterator);

        $this->flags = $flags;
    }

    /**
     * @inheritDoc
     */
    public function accept()
    {
        $current = $this->current();

        $result = !in_array($this->formatValue($current), $this->exists);

        if ($result) {
            $this->exists[] = $current;
        }

        return $result;
    }

    /**
     * formatValue
     *
     * @param mixed $value
     *
     * @return  mixed
     */
    protected function formatValue($value)
    {
        switch ($this->flags) {
            case SORT_NUMERIC:
                return (float) $value;

            case SORT_STRING:
            case SORT_LOCALE_STRING:
                return (string) $value;

            case SORT_REGULAR:
            default:
                return $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->exists = [];

        parent::rewind();
    }

    /**
     * Method to set property strict
     *
     * @param  bool  $flags
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setFlags(bool $flags)
    {
        $this->flags = $flags;

        return $this;
    }
}
