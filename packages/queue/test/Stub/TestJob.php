<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Queue\Test\Stub;

use Windwalker\Queue\Job\JobInterface;

/**
 * The TestJob class.
 */
class TestJob implements JobInterface
{
    public array $logs = [];

    public ?array $executed = null;

    /**
     * TestJob constructor.
     *
     * @param  array  $logs
     */
    public function __construct(array $logs = [])
    {
        $this->logs = $logs;
    }

    /**
     * getName
     *
     * @return  string
     */
    public function getName(): string
    {
        return 'test';
    }

    /**
     * execute
     *
     * @return  void
     */
    public function execute(): void
    {
        $this->executed = $this->logs;
    }
}
