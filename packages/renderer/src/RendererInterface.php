<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Renderer;

/**
 * Interface RendererInterface
 */
interface RendererInterface
{
    /**
     * render
     *
     * @param  string  $layout
     * @param  array   $data
     * @param  array   $options
     *
     * @return  string
     */
    public function render(string $layout, array $data = [], array $options = []): string;
}
