<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise\Exception;

/**
 * The UncaughtException class.
 */
class UncaughtException extends \Exception
{
    private $reason;

    /**
     * UncaughtException constructor.
     *
     * @param  mixed            $reason
     * @param  \Throwable|null  $previous
     */
    public function __construct($reason, ?\Throwable $previous = null)
    {
        $this->reason = $reason;

        parent::__construct('', 0, $previous);
    }

    /**
     * Method to get property Reason
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getReason()
    {
        return $this->reason;
    }
}
