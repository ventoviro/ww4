<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Event;

use Windwalker\Event\AbstractEvent;

/**
 * The QueryStartEvent class.
 */
class QueryStartEvent extends AbstractEvent
{
    protected ?array $params = [];

    /**
     * @return array|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @param  array|null  $params
     *
     * @return  static  Return self to support chaining.
     */
    public function setParams(?array $params)
    {
        $this->params = $params;

        return $this;
    }
}
