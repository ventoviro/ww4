<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Renderer;

/**
 * The MustacheRenderer class.
 *
 * @since  2.0
 */
class MustacheRenderer extends AbstractEngineRenderer
{
    /**
     * Property engine.
     *
     * @var \Mustache_Engine
     */
    protected ?object $engine;

    /**
     * Property loader.
     *
     * @var \Mustache_Loader
     */
    protected $loader;

    /**
     * render
     *
     * @param  string  $layout
     * @param  array   $data
     *
     * @param  array   $options
     *
     * @return  string
     */
    public function render(string $layout, array $data = [], array $options = []): string
    {
        $engine = $this->getEngine([]);

        $path = $this->findFile($layout);

        $engine->setLoader($this->getLoader(dirname($path)));

        return $engine->render($layout, $data);
    }

    /**
     * findFile
     *
     * @param  string       $file
     * @param  string|null  $ext
     *
     * @return string|null
     */
    public function findFile(string $file, ?string $ext = null): ?string
    {
        $ext ??= $this->getOption('file_ext', 'mustache');

        return parent::findFile($file, $ext);
    }

    /**
     * Method to get property Engine
     *
     * @param  array    $options
     * @param  boolean  $new
     *
     * @return \Mustache_Engine
     */
    public function getEngine(array $options = [], bool $new = false)
    {
        if (!$this->engine || $new) {
            $this->engine = new \Mustache_Engine($this->getOption('options', []));
        }

        return $this->engine;
    }

    /**
     * Method to set property engine
     *
     * @param  object|null  $engine
     *
     * @return MustacheRenderer Return self to support chaining.
     */
    public function setEngine(?object $engine)
    {
        if (!($engine instanceof \Mustache_Engine)) {
            throw new \InvalidArgumentException('Engine object should be Mustache_Engine');
        }

        $this->engine = $engine;

        return $this;
    }

    /**
     * Method to get property Loader
     *
     * @param string $path
     *
     * @return  \Mustache_Loader
     */
    public function getLoader($path = null)
    {
        if (!$this->loader) {
            $options = [
                // 'file_ext' => '.html'
            ];

            $options = array_merge($options, (array) $this->getOption('loader_options', []));

            $this->loader = new \Mustache_Loader_FilesystemLoader($path, $options);
        }

        return $this->loader;
    }

    /**
     * Method to set property loader
     *
     * @param   \Mustache_Loader $loader
     *
     * @return  static  Return self to support chaining.
     */
    public function setLoader($loader)
    {
        $this->loader = $loader;

        return $this;
    }

    public function reset(): void
    {
        $this->engine = null;
        $this->loader = null;
    }
}
