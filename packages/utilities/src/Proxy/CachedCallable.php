<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Proxy;

use function Windwalker\tap;

/**
 * The CachedCallable class.
 */
class CachedCallable extends DisposableCallable
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @inheritDoc
     */
    public function __invoke(...$args)
    {
        if ($this->called) {
            return $this->value;
        }

        return tap(parent::__invoke(...$args), function ($value) {
            $this->value = $value;
            $this->called = true;
        });
    }
}
