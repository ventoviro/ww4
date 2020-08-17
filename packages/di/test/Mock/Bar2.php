<?php declare(strict_types=1);
/**
 * Part of windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\DI\Test\Mock;

/**
 * The Bar class.
 *
 * @since  2.0
 */
class Bar2
{
    /**
     * Property queue.
     *
     * @var  \SplPriorityQueue
     */
    public $queue = null;

    /**
     * Property stack.
     *
     * @var  \SplStack
     */
    public $stack = null;

    /**
     * Class init.
     *
     * @param \SplPriorityQueue $queue
     * @param \SplStack         $stack
     */
    public function __construct(\SplPriorityQueue $queue, \SplStack $stack)
    {
        $this->queue = $queue;
        $this->stack = $stack;
    }
}
