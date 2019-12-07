<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Event\Test\Provider;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Windwalker\Event\EventInterface;
use Windwalker\Event\EventSubscriberInterface;
use Windwalker\Event\Listener\ListenerItem;
use Windwalker\Event\Listener\ListenersQueue;
use Windwalker\Event\Provider\SubscribableListenerProvider;
use Windwalker\Utilities\TypeCast;

/**
 * The StandardListenerProviderTest class.
 */
class SubscribableListenerProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var SubscribableListenerProvider
     */
    protected $instance;

    /**
     * @see  SubscribableListenerProvider::subscribe
     */
    public function testSubscribe(): void
    {
        $subscriber = new class implements EventSubscriberInterface {
            /**
             * @inheritDoc
             */
            public function getSubscribedEvents(): array
            {
                return [
                    'flower.sakura' => 'onFlowerSakura',
                    'flower.rose' => ['onFlowerRose', 50],
                    'flower.olive' => [
                        ['onFlowerRose', 30],
                        ['onFlowerOlive', 100]
                    ]
                ];
            }

            public function onFlowerSakura($event)
            {
                $event->foo(2);
            }

            public function onFlowerRose($event)
            {
                $event->foo(3);
            }

            public function onFlowerOlive($event)
            {
                $event->foo(1);
            }
        };

        $this->instance->subscribe($subscriber);

        $event = \Mockery::mock(EventInterface::class);
        $event->shouldReceive('getName')->andReturn('flower.sakura')->getMock();
        $event->shouldReceive('foo')->with(2)->getMock();

        TypeCast::toArray($this->instance->getListenersForEvent($event))[0]($event);

        $event = \Mockery::mock(EventInterface::class);
        $event->shouldReceive('getName')->andReturn('flower.rose')->getMock();
        $event->shouldReceive('foo')->with(3)->getMock();

        TypeCast::toArray($this->instance->getListenersForEvent($event))[0]($event);

        $event = \Mockery::mock(EventInterface::class);
        $event->shouldReceive('getName')->andReturn('flower.olive')->getMock();

        /** @var ListenerItem[] $listeners */
        $listeners = array_values(TypeCast::toArray($this->instance->getListenersForEvent($event)));

        self::assertSame($subscriber, $listeners[0]->getCallable()[0]);
        self::assertEquals('onFlowerOlive', $listeners[0]->getCallable()[1]);
        self::assertEquals('onFlowerRose', $listeners[1]->getCallable()[1]);
    }

    /**
     * @see  SubscribableListenerProvider::on
     * @see  SubscribableListenerProvider::getListenersForEvent
     */
    public function testGetListenersForEvent(): void
    {
        $event = \Mockery::mock(EventInterface::class);
        $event->shouldReceive('getName')->andReturn('HelloEvent');
        $event->shouldReceive('hello');

        $this->instance->on($event->getName(), $expt = function ($event) {
            $event->hello();
        });

        $handlers = TypeCast::toArray($this->instance->getListenersForEvent($event));

        self::assertSame($expt, $handlers[0]->getCallable());

        $handlers[0]($event);
    }

    /**
     * @see  SubscribableListenerProvider::getQueues
     */
    public function testGetListeners(): void
    {
        $this->instance->on('hello', $fn1 = $this->nope());
        $this->instance->on('hello', $fn2 = $this->nope());
        $this->instance->on('world', $fn3 = $this->nope());

        $listeners = $this->instance->getQueues();

        self::assertInstanceOf(ListenersQueue::class, $listeners['hello']);
        self::assertInstanceOf(ListenersQueue::class, $listeners['world']);
        self::assertSame($fn2, array_values(TypeCast::toArray($listeners['hello']))[1]->getCallable());
        self::assertSame($fn3, array_values(TypeCast::toArray($listeners['world']))[0]->getCallable());
    }

    protected function setUp(): void
    {
        $this->instance = new SubscribableListenerProvider();
    }

    protected function tearDown(): void
    {
        //
    }

    protected function nope()
    {
        return function () {
            //
        };
    }
}
