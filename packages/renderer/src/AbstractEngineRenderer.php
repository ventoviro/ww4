<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Renderer;

/**
 * The AbstractEngineRenderer class.
 *
 * @since  2.0
 */
abstract class AbstractEngineRenderer extends AbstractRenderer
{
    /**
     * Property engine.
     *
     * @var object|null
     */
    protected object|null $engine = null;

    /**
     * createWithEngine
     *
     * @param  object  $engine
     * @param  array   $options
     *
     * @return  static
     */
    public static function createWithEngine(object $engine, array $options = [])
    {
        $renderer = new static([], $options);
        $renderer->setEngine($engine);

        return $renderer;
    }

    /**
     * Method to get property Engine
     *
     * @param  array    $options
     * @param  boolean  $new
     *
     * @return object
     */
    abstract public function getEngine(array $options = [], bool $new = false);

    /**
     * Method to set property engine
     *
     * @param  object|null  $engine
     *
     * @return  static to support chaining.
     */
    abstract public function setEngine(?object $engine);

    abstract public function reset(): void;
}
