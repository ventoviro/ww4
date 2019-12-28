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
 * The TimesLimitedCallable class.
 */
class TimesLimitedCallable extends CallableProxy
{
    /**
     * @var int|null
     */
    protected $limits = null;

    /**
     * @var int
     */
    protected $callTimes = 0;

    /**
     * TimesLimitedCallable constructor.
     *
     * @param  callable  $callable
     * @param  int       $limits
     */
    public function __construct(callable $callable, ?int $limits = null)
    {
        parent::__construct($callable);

        $this->limits = $limits;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(...$args)
    {
        if ($this->isOverLimits()) {
            return;
        }

        return tap(
            parent::__invoke(...$args),
            function () {
                $this->callTimes++;
            }
        );
    }

    /**
     * Method to get property CallTimes
     *
     * @return  int
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getCallTimes(): int
    {
        return $this->callTimes;
    }

    /**
     * Method to get property Limits
     *
     * @return  int
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getLimits(): int
    {
        return $this->limits;
    }

    /**
     * isOverLimits
     *
     * @return  bool
     */
    public function isOverLimits(): bool
    {
        return $this->limits !== null && $this->callTimes >= $this->limits;
    }
}
