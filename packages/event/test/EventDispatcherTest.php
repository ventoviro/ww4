<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Event\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Event\EventDispatcher;
use Windwalker\Event\Provider\SimpleListenerProvider;
use Windwalker\Event\Test\Stub\StubFlowerEvent;

/**
 * The EventDispatcherTest class.
 */
class EventDispatcherTest extends TestCase
{
    /**
     * @var EventDispatcher
     */
    protected $instance;

    /**
     * @see  EventDispatcher::setListenerProvider
     */
    public function testSetListenerProvider(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  EventDispatcher::__construct
     */
    public function testConstruct(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  EventDispatcher::dispatch
     */
    public function testDispatch(): void
    {
        $value = null;

        $listener = function () use (&$value) {
            $value = 'Hello';
        };

        $d = new EventDispatcher(new SimpleListenerProvider([
            StubFlowerEvent::class => [$listener]
        ]));

        $d->dispatch(new StubFlowerEvent());

        self::assertEquals('Hello', $value);
    }

    /**
     * @see  EventDispatcher::getListenerProvider
     */
    public function testGetListenerProvider(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  EventDispatcher::emit
     */
    public function testEmit(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        // $this->instance = new EventDispatcher(new SimpleListenerProvider([]));
    }

    protected function tearDown(): void
    {
    }
}
