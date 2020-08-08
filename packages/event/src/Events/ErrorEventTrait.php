<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Event\Events;

/**
 * Trait ErrorEventTrait
 */
trait ErrorEventTrait
{
    /**
     * @var \Throwable
     */
    protected \Throwable $exception;

    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function setException(\Throwable $exception): static
    {
        $this->exception = $exception;

        return $this;
    }
}
