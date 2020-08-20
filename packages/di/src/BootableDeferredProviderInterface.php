<?php

/**
 * Part of phoenix project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\DI;

/**
 * Interface BootableDeferredProviderInterface
 *
 * @since  3.5
 */
interface BootableDeferredProviderInterface
{
    /**
     * boot
     *
     * @param Container $container
     *
     * @return  void
     */
    public function bootDeferred(Container $container): void;
}
