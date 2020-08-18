<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\DI\Test\Attributes;

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
     * @Inject(key="stub")
     *
     * @var StubService
     */
    @@Inject('stub')
    public StubService $baz;

    /**
     * @Inject(key="stub", new=true)
     *
     * @var StubService
     */
    @@Inject('stub', true)
    public StubService $yoo;
}
