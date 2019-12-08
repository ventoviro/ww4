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
use Rx\Observable;
use Rx\ObserverInterface;
use Rx\Scheduler;

/**
 * The RxTest class.
 */
class RxTest extends TestCase
{
    public function testObservable()
    {
        $observable = Observable::create(function (ObserverInterface $observer) {
            $observer->onNext('Hello');
        });

        $observable->subscribe(function (...$args) {
            show('sub', $args);
        });

        $scheduler = Scheduler::getImmediate();

        show($scheduler);

        self::assertEquals('', 123);
    }
}
