<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Renderer;

/**
 * The RendererFactory class.
 */
class RendererFactory
{
    public static function createRenderer(string $type, string|array|\SplPriorityQueue $paths, array $options = []): RendererInterface
    {
        $class = static::getClassName($type);

        return $class($paths, $options);
    }

    public static function createWithEngine(string $type, ?object $engine = null, array $options = []): RendererInterface
    {
        $class = static::getClassName($type);

        if (is_subclass_of($class, AbstractEngineRenderer::class)) {
            return $class::createWithEngine($engine, $options);
        }

        return self::createRenderer($type, $options);
    }

    /**
     * getClassName
     *
     * @param  string  $type
     *
     * @return  string
     */
    protected static function getClassName(string $type): string
    {
        return 'Windwalker\Renderer\\' . ucfirst($type) . 'Renderer';
    }
}
