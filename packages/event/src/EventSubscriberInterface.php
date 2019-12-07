<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Event;

/**
 * Interface EventSubscriberInterface
 */
interface EventSubscriberInterface
{
    /**
     * Get the event subscriber settings.
     *
     * Example:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority, $once = false]]
     *  * ['eventName' => [['methodName1', $priority, $once = false], ['methodName2']]]
     *
     * @return  mixed
     */
    public function getSubscribedEvents(): array;
}
