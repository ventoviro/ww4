<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\DI\Test\Injection;

use Windwalker\DI\Attributes\Inject;

/**
 * The StubInject class.
 *
 * @since  3.4.4
 */
class StubInject
{
    /**
     * @Inject
     *
     * @var StubService
     */
    @@Inject
    public ?StubService $foo = null;

    /**
     * @Inject
     *
     * @var StubService
     */
    @@Inject
    protected ?StubService $bar = null;

    /**
     * @var StubService
     */
    @@Inject('stub')
    public StubService $baz;

    /**
     * @var StubService
     */
    @@Inject('stub', true)
    public StubService $yoo;
}
