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
     * @var bool
     */
    protected $strict = false;

    /**
     * @inheritDoc
     */
    public function __construct(Iterator $iterator, bool $strict = false)
    {
        parent::__construct($iterator);

        $this->strict = $strict;
    }

    /**
     * @inheritDoc
     */
    public function accept()
    {
        $current = $this->current();

        $result = !in_array($current, $this->exists, $this->strict);

        if ($result) {
            $this->exists[] = $current;
        }

        return $result;
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
     * @param  bool  $strict
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setStrict(bool $strict)
    {
        $this->strict = $strict;

        return $this;
    }
}
