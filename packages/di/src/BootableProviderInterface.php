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
 * Interface BootableProviderInterface
 *
 * @since  3.5
 */
interface BootableProviderInterface
{
    /**
     * boot
     *
     * @param Container $container
     *
     * @return  void
     */
    public function boot(Container $container): void;
}
