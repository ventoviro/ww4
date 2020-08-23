<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Renderer\Twig;

use Twig\Extension\ExtensionInterface;

/**
 * The GlobalContainer class.
 *
 * @since  2.0
 */
abstract class GlobalContainer
{
    /**
     * Property extensions.
     *
     * @var  \Twig_ExtensionInterface[]
     */
    protected static $extensions = [];

    /**
     * Property data.
     *
     * @var  array
     */
    protected static $globals = [];

    /**
     * addExtension
     *
     * @param  string              $name
     * @param  ExtensionInterface  $extension
     *
     * @return  void
     */
    public static function addExtension($name, ExtensionInterface $extension)
    {
        static::$extensions[$name] = $extension;
    }

    /**
     * getExtension
     *
     * @param  string  $name
     *
     * @return  ExtensionInterface
     */
    public static function getExtension($name)
    {
        if (!empty(static::$extensions[$name])) {
            return static::$extensions[$name];
        }

        return null;
    }

    /**
     * removeExtension
     *
     * @param  string  $name
     *
     * @return  void
     */
    public static function removeExtension($name)
    {
        if (isset(static::$extensions[$name])) {
            unset(static::$extensions[$name]);
        }
    }

    /**
     * Method to get property Extensions
     *
     * @return  ExtensionInterface[]
     */
    public static function getExtensions(): array
    {
        return static::$extensions;
    }

    /**
     * Method to set property extensions
     *
     * @param  ExtensionInterface[]  $extensions
     *
     * @return  void
     */
    public static function setExtensions(array $extensions): void
    {
        static::$extensions = $extensions;
    }

    /**
     * setGlobal
     *
     * @param  string  $name
     * @param  mixed   $value
     *
     * @return  void
     */
    public static function addGlobal(string $name, $value): void
    {
        static::$globals[$name] = $value;
    }

    /**
     * getGlobal
     *
     * @param  string  $name
     *
     * @return  mixed
     */
    public static function getGlobal(string $name)
    {
        if (array_key_exists($name, static::$globals)) {
            return static::$globals[$name];
        }

        return null;
    }

    /**
     * removeGlobal
     *
     * @param  string  $name
     *
     * @return  void
     */
    public static function removeGlobal(string $name)
    {
        if (isset(static::$globals[$name])) {
            unset(static::$globals[$name]);
        }
    }

    /**
     * Method to get property Globals
     *
     * @return  array
     */
    public static function getGlobals(): array
    {
        return static::$globals;
    }

    /**
     * Method to set property globals
     *
     * @param  array  $globals
     *
     * @return  void
     */
    public static function setGlobals(array $globals): void
    {
        static::$globals = $globals;
    }
}
