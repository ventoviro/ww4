<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Event\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * The DecorateListenerProvider class.
 */
class DecorateListenerProvider implements ListenerProviderInterface
{
    /**
     * @var ListenerProviderInterface
     */
    protected $provider;

    /**
     * DecorateListenerProvider constructor.
     *
     * @param  ListenerProviderInterface  $provider
     */
    public function __construct(ListenerProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        return $this->provider->getListenersForEvent($event);
    }
}
