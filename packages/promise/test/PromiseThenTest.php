<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Promise\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Promise\Async\AsyncRunner;
use Windwalker\Promise\Async\NoAsync;
use Windwalker\Promise\Promise;
use Windwalker\Test\TestHelper;
use Windwalker\Test\Traits\TestAccessorTrait;

use function Windwalker\Promise\nope;

/**
 * The SwoolePromiseTest class.
 *
 * @since  __DEPLOY_VERSION__
 */
class PromiseThenTest extends TestCase
{
    use TestAccessorTrait;

    /**
     * @var array
     */
    protected $values = '';

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        AsyncRunner::getInstance()->setHandlers(
            [
                new NoAsync()
            ]
        );
    }

    /**
     * @throws \ReflectionException
     * @see  Promise::then
     */
    public function testThenWithPending(): void
    {
        $p = new Promise(nope());

        $p2 = $p->then($rsv1 = nope(), $rej1 = nope());
        $p->then($rsv2 = nope(), $rej2 = nope());

        $p3 = $p2->then($rsv3 = nope(), $rej3 = nope());

        self::assertNotSame($p2, $p);

        // Handlers
        $handlers = TestHelper::getValue($p, 'handlers');

        self::assertSame($handlers[0][0], $p2);
        self::assertSame($handlers[0][1], $rsv1);
        self::assertSame($handlers[0][2], $rej1);
        self::assertSame($handlers[1][1], $rsv2);

        $handlers = TestHelper::getValue($p2, 'handlers');

        self::assertSame($handlers[0][0], $p3);
        self::assertSame($handlers[0][1], $rsv3);
        self::assertSame($handlers[0][2], $rej3);
    }

    /**
     * @throws \ReflectionException
     * @see  Promise::then
     */
    public function testThenAlreadyFulfilled(): void
    {
        $p = new Promise(
            function ($resolve, $reject) {
                $resolve(1);
            }
        );

        $state = TestHelper::getValue($p, 'state');

        self::assertEquals(Promise::FULFILLED, $state);

        $p2 = $p
            ->then(
                function ($v) {
                    return ++$v;
                }
            )
            ->then(
                function ($v) {
                    return ++$v;
                }
            );

        self::assertEquals(3, TestHelper::getValue($p2, 'value'));

        // Test return new Promise
        $p3 = $p->then(
            function ($v) {
                return new Promise(
                    function ($resolve) {
                        $resolve('Hello');
                    }
                );
            }
        );

        $newValue = null;

        $p4 = $p3->then(
            function ($v2) use (&$newValue) {
                $newValue = $v2;
            }
        );

        self::assertEquals('Hello', TestHelper::getValue($p3, 'value'));
        self::assertNull(TestHelper::getValue($p4, 'value'));
        self::assertEquals('Hello', $newValue);
    }

    public function testThenAndResolveDeferred(): void
    {
        $p = new Promise(
            function () {
                $this->values['v0'] = 'Init';
            }
        );

        $p
            ->then(
                function ($v) {
                    $this->values['v1'] = $v;

                    return $v . ' World';
                },
                function ($r) {
                    $this->values['r1'] = $r;

                    return $r;
                }
            )
            ->then(
                function ($v) {
                    $this->values['v2'] = $v;

                    return $v;
                },
                function ($r) {
                    $this->values['r2'] = $r;

                    return $r;
                }
            );

        $p->resolve('Hello');

        self::assertEquals('Hello', $this->values['v1']);
        self::assertEquals('Hello World', $this->values['v2']);
        self::assertArrayNotHasKey('r1', $this->values);
        self::assertArrayNotHasKey('r2', $this->values);
    }

    /**
     * @throws \ReflectionException
     * @see  Promise::then
     */
    public function testThenAlreadyRejected(): void
    {
        $p = new Promise(
            function ($resolve, $reject) {
                $reject($this->values['e1'] = new \Exception('Sakura'));
            }
        );

        self::assertEquals(Promise::REJECTED, $p->getState());

        $p2 = $p
            ->then(
                null,
                function (\Exception $e) {
                    $this->values['e2'] = $e;

                    return 'New state';
                }
            )
            ->then(
                function ($v) {
                    $this->values['t1'] = $v;

                    return $v;
                },
                function ($r) {
                    $this->values['t2'] = $r;

                    return $r;
                }
            );

        self::assertSame($this->values['e1'], $this->values['e2']);
        self::assertEquals('New state', $this->values['t1']);
        self::assertArrayNotHasKey('t2', $this->values);

        // Test return new Promise
        $p3 = $p->then(
            null,
            function ($reason) {
                return new Promise(
                    function ($resolve) {
                        $resolve('Hello');
                    }
                );
            }
        );

        $newValue = null;

        $p4 = $p3->then(
            function ($v2) use (&$newValue) {
                $newValue = $v2;
            }
        );

        self::assertEquals('Hello', $this->getValue($p3, 'value'));
        self::assertNull($this->getValue($p4, 'value'));
        self::assertEquals('Hello', $newValue);
    }

    public function testThenAndRejectDeferred()
    {
        $p = new Promise(
            function () {
                $this->values['v0'] = 'Init';
            }
        );

        $p2 = $p->then(
            function ($v) {
                $this->values['v1'] = $v;

                return $v;
            },
            function (\Exception $r) {
                $this->values['r1'] = $r;

                return $r->getMessage() . ' World';
            }
        );

        $p3 = $p2->then(
            function ($v) {
                $this->values['v2'] = $v;

                return $v;
            },
            function ($r) {
                $this->values['r2'] = $r;

                return $r;
            }
        );

        $p->reject($e = new \Exception('Hello'));

        self::assertSame($e, $this->values['r1']);
        self::assertEquals('Hello World', $this->values['v2']);
        self::assertArrayNotHasKey('v1', $this->values);
        self::assertArrayNotHasKey('r2', $this->values);

        self::assertEquals(Promise::REJECTED, $p->getState());
        self::assertEquals(Promise::FULFILLED, $p2->getState());
    }

    public function testThenWithNope(): void
    {
        $p1 = Promise::create();
        // Give no functions to then(), will just return same state Promise and same values.
        $p2 = $p1->then()
            ->then(
                function ($v) {
                    $this->values['v1'] = $v;

                    throw new \Exception('Sakura');
                }
            )
            ->then()
            ->then(
                null,
                function ($r) {
                    $this->values['r1'] = $r;

                    return 'Olive';
                }
            )
            // If onRejected called, back to fulfilled
            ->then(
                function ($v) {
                    $this->values['v2'] = $v;
                }
            );

        $p1->resolve('Rose');

        self::assertEquals('Rose', $this->values['v1']);
        self::assertEquals('Sakura', $this->values['r1']->getMessage());
        self::assertEquals('Olive', $this->values['v2']);
    }

    /**
     * @see  PromiseThen::then
     */
    public function testThenWithRejectedPromise(): void
    {
        Promise::create(
            function ($re) {
                $re('Hello');
            }
        )
            ->then(
                function () {
                    $this->values['p2'] = $p2 = new Promise(
                        function ($re2, $rj2) {
                            $rj2('Error');
                        }
                    );

                    $p2->then(
                        nope(),
                        function ($reason) {
                            $this->values['r1'] = $reason;
                        }
                    );

                    return $p2;
                }
            )
            ->then(
                function ($v) {
                    $this->values['v1'] = $v;
                },
                function ($r2) {
                    $this->values['r2'] = $r2;
                }
            );

        self::assertEquals('Error', $this->values['r1']);
        self::assertEquals('Error', $this->values['r2']);
    }

    public function testThenWithRejectedThenable(): void
    {
        Promise::create(
            function ($re) {
                $re('Hello');
            }
        )
            ->then(
                function () {
                    $this->values['p2'] = $p2 = new Promise(
                        function ($re2, $rj2) {
                            $rj2('Error');
                        }
                    );

                    $p2->then(
                        nope(),
                        function ($reason) {
                            $this->values['r1'] = $reason;
                        }
                    );

                    return $p2;
                }
            )
            ->then(
                function ($v) {
                    $this->values['v1'] = $v;
                },
                function ($r2) {
                    $this->values['r2'] = $r2;
                }
            );

        self::assertEquals('Error', $this->values['r1']);
        self::assertEquals('Error', $this->values['r2']);
    }

    protected function setUp(): void
    {
        $this->values = [];
    }
}
