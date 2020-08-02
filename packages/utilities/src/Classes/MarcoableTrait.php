<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Utilities\Classes;

use BadMethodCallException;
use Closure;

/**
 * The MarcoableTrait class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait MarcoableTrait
{
    /**
     * @var  callable[]
     */
    protected static array $macros = [];

    public static function macro(string $name, callable $macro): void
    {
        static::$macros[$name] = $macro;
    }

    public function do(string $name, ...$args)
    {
        return $this->__call($name, $args);
    }

    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    public static function clearMarco(): void
    {
        static::$macros = [];
    }

    public static function __callStatic($name, $args)
    {
        if (!static::hasMacro($name)) {
            throw new BadMethodCallException(
                sprintf(
                    'Method %s::%s does not exist.',
                    static::class,
                    $name
                )
            );
        }

        $macro = static::$macros[$name];

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo(null, static::class);
        }

        return $macro(...$args);
    }

    public function __call($name, $args)
    {
        if (!static::hasMacro($name)) {
            throw new BadMethodCallException(
                sprintf(
                    'Method %s::%s does not exist.',
                    static::class,
                    $name
                )
            );
        }

        $macro = static::$macros[$name];

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo($this, static::class);
        }

        return $macro(...$args);
    }
}
