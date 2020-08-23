<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Renderer;

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as BladeEnvironment;
use Illuminate\View\FileViewFinder;
use Windwalker\Renderer\Blade\BladeExtending;
use Windwalker\Renderer\Blade\GlobalContainer;
use Windwalker\Utilities\TypeCast;

/**
 * The BladeRenderer class.
 *
 * @since  2.0
 */
class BladeRenderer extends AbstractEngineRenderer
{
    /**
     * Property blade.
     *
     * @var  ?BladeEnvironment
     */
    protected ?object $engine = null;

    /**
     * Property filesystem.
     *
     * @var Filesystem
     */
    protected ?Filesystem $filesystem = null;

    /**
     * Property finder.
     *
     * @var FileViewFinder
     */
    protected ?FileViewFinder $finder = null;

    /**
     * Property resolver.
     *
     * @var EngineResolver
     */
    protected ?EngineResolver $resolver = null;

    /**
     * Property dispatcher.
     *
     * @var Dispatcher
     */
    protected ?Dispatcher $dispatcher = null;

    /**
     * Property compiler.
     *
     * @var CompilerEngine
     */
    protected ?CompilerEngine $compiler = null;

    /**
     * Property customCompiler.
     *
     * @var  callable[]
     */
    protected array $customCompilers = [];

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
        TypeCast::toArray($data);

        return $this->getEngine([])->make($layout, (array) $data)->render();
    }

    /**
     * Method to get property Blade
     *
     * @param  array  $options
     * @param  bool   $new
     *
     * @return BladeEnvironment
     */
    public function getEngine(array $options = [], bool $new = false)
    {
        if (!$this->engine || $new) {
            $this->engine = new BladeEnvironment($this->getResolver(), $this->getFinder(), $this->getDispatcher());

            /** @var BladeCompiler $bladeCompiler */
            $bladeCompiler = $this->getCompiler()->getCompiler();

            foreach (GlobalContainer::getCompilers() as $name => $callback) {
                BladeExtending::extend($bladeCompiler, $name, $callback);
            }

            foreach ($this->getCustomCompilers() as $name => $callback) {
                BladeExtending::extend($bladeCompiler, $name, $callback);
            }

            foreach (GlobalContainer::getExtensions() as $name => $callback) {
                $bladeCompiler->extend($callback);
            }

            // B/C for 4.* and 5.*
            if (($rawTags = GlobalContainer::getRawTags()) && is_callable([$bladeCompiler, 'setRawTags'])) {
                $bladeCompiler->setRawTags($rawTags[0], $rawTags[1]);
            }

            if ($tags = GlobalContainer::getContentTags()) {
                $bladeCompiler->setContentTags($tags[0], $tags[1]);
            }

            if ($tags = GlobalContainer::getEscapedTags()) {
                $bladeCompiler->setEscapedContentTags($tags[0], $tags[1]);
            }
        }

        return $this->engine;
    }

    /**
     * Method to set property blade
     *
     * @param  object|null  $engine
     *
     * @return BladeRenderer Return self to support chaining.
     */
    public function setEngine(?object $engine)
    {
        if (!($engine instanceof BladeEnvironment)) {
            throw new \InvalidArgumentException('Engine object should be Illuminate\View\Environment.');
        }

        $this->engine = $engine;

        return $this;
    }

    /**
     * Method to get property Filesystem
     *
     * @return  Filesystem
     */
    public function getFilesystem()
    {
        if (!$this->filesystem) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    /**
     * Method to set property filesystem
     *
     * @param   Filesystem $filesystem
     *
     * @return  static  Return self to support chaining.
     */
    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Method to get property Finder
     *
     * @return  FileViewFinder
     */
    public function getFinder()
    {
        if (!$this->finder) {
            $this->finder = new FileViewFinder($this->getFilesystem(), $this->dumpPaths());
        }

        return $this->finder;
    }

    /**
     * Method to set property finder
     *
     * @param   FileViewFinder $finder
     *
     * @return  static  Return self to support chaining.
     */
    public function setFinder($finder)
    {
        $this->finder = $finder;

        return $this;
    }

    /**
     * Method to get property Resolver
     *
     * @return  EngineResolver
     */
    public function getResolver()
    {
        if (!$this->resolver) {
            $self = $this;

            $this->resolver = new EngineResolver();

            $this->resolver->register(
                'blade',
                function () use ($self) {
                    return $self->getCompiler();
                }
            );
        }

        return $this->resolver;
    }

    /**
     * Method to set property resolver
     *
     * @param   EngineResolver $resolver
     *
     * @return  static  Return self to support chaining.
     */
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * Method to get property Dispatcher
     *
     * @return  Dispatcher
     */
    public function getDispatcher()
    {
        if (!$this->dispatcher) {
            $this->dispatcher = new Dispatcher();
        }

        return $this->dispatcher;
    }

    /**
     * Method to set property dispatcher
     *
     * @param   Dispatcher $dispatcher
     *
     * @return  static  Return self to support chaining.
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * Method to get property Compiler
     *
     * @return  CompilerEngine
     */
    public function getCompiler(): CompilerEngine
    {
        if (!$this->compiler) {
            $cachePath = $this->getOption('cache_path') ?: GlobalContainer::getCachePath();

            if (!$cachePath) {
                throw new \InvalidArgumentException('Please set cache_path into config.');
            }

            if (!is_dir($cachePath)) {
                mkdir($cachePath, 0755, true);
            }

            $this->compiler = new CompilerEngine(new BladeCompiler($this->getFilesystem(), $cachePath));
        }

        return $this->compiler;
    }

    /**
     * Method to set property compiler
     *
     * @param   CompilerEngine $compiler
     *
     * @return  static  Return self to support chaining.
     */
    public function setCompiler($compiler)
    {
        $this->compiler = $compiler;

        return $this;
    }

    /**
     * addCustomCompiler
     *
     * @param   string   $name
     * @param   callable $compiler
     *
     * @return  static
     */
    public function addCustomCompiler($name, $compiler)
    {
        if (!is_callable($compiler)) {
            throw new \InvalidArgumentException('Compiler should be callable.');
        }

        $this->customCompilers[$name] = $compiler;

        return $this;
    }

    /**
     * Method to get property CustomCompiler
     *
     * @return  \callable[]
     */
    public function getCustomCompilers()
    {
        return $this->customCompilers;
    }

    /**
     * Method to set property customCompiler
     *
     * @param   \callable[] $customCompilers
     *
     * @return  static  Return self to support chaining.
     */
    public function setCustomCompilers(array $customCompilers)
    {
        $this->customCompilers = $customCompilers;

        return $this;
    }

    public function reset(): void
    {
        $this->engine = null;
        $this->filesystem = null;
        $this->finder = null;
        $this->resolver = null;
        $this->dispatcher = null;
        $this->compiler = null;
        $this->customCompilers = [];
    }
}
