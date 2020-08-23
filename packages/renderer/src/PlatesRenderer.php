<?php declare(strict_types=1);
/**
 * Part of windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Renderer;

use League\Plates\Engine as PlatesEngine;
use League\Plates\Extension;
use League\Plates\Extension\ExtensionInterface;

/**
 * The PlatesRenderer class.
 *
 * @since  2.0.9
 */
class PlatesRenderer extends AbstractEngineRenderer
{
    /**
     * Property extensions.
     *
     * @var  ExtensionInterface[]
     */
    protected array $extensions = [];

    /**
     * Property folders.
     *
     * @var  array
     */
    protected array $folders = [];

    /**
     * Method to get property Engine
     *
     * @param  array    $options
     * @param  boolean  $new
     *
     * @return  PlatesEngine
     */
    public function getEngine(array $options = [], bool $new = false): PlatesEngine
    {
        if (!$this->engine || $new) {
            $this->engine = PlatesEngine::create(
                $options['path'] ?? '',
                ltrim($this->getOption('file_ext', '.phtml'), '.')
            );

            foreach ($this->folders as $namespace => $folder) {
                $this->engine->addFolder($namespace, $folder['folder'], $folder['fallback']);
            }

            foreach ($this->extensions as $extension) {
                $this->engine->loadExtension($extension);
            }
        }

        return $this->engine;
    }

    /**
     * Method to set property engine
     *
     * @param  object|null  $engine
     *
     * @return PlatesRenderer Return self to support chaining.
     */
    public function setEngine(?object $engine)
    {
        if (!($engine instanceof PlatesEngine)) {
            throw new \InvalidArgumentException('Engine object should be Mustache_Engine');
        }

        $this->engine = $engine;

        return $this;
    }

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
        $path = $this->findFile($layout);

        return $this->getEngine(['path' => dirname($path)], true)->render($layout, $data);
    }

    /**
     * findFile
     *
     * @param  string       $file
     * @param  string|null  $ext
     *
     * @return string|null
     */
    public function findFile(string $file, ?string $ext = ''): ?string
    {
        $ext = $ext ?: trim($this->getOption('file_ext', 'phtml'), '.');

        return parent::findFile($file, $ext);
    }

    /**
     * addExtension
     *
     * @param Extension $extension
     *
     * @return  static
     */
    public function addExtension(Extension $extension)
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /**
     * addFolder
     *
     * @param  string   $namespace
     * @param  string   $folder
     * @param  boolean  $fallback
     *
     * @return  static
     */
    public function addFolder(string $namespace, string $folder, bool $fallback = false)
    {
        $this->folders[$namespace] = [
            'folder' => $folder,
            'fallback' => $fallback,
        ];

        return $this;
    }

    public function reset(): void
    {
        $this->extensions = [];
        $this->folders = [];
    }
}
