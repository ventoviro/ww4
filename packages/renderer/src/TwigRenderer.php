<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Renderer;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\LoaderInterface;
use Windwalker\Renderer\Twig\GlobalContainer;
use Windwalker\Renderer\Twig\TwigFilesystemLoader;

/**
 * Class PhpRenderer
 *
 * @since 2.0
 */
class TwigRenderer extends AbstractEngineRenderer
{
    /**
     * Property twig.
     *
     * @var  ?Environment
     */
    protected ?object $engine = null;

    /**
     * Property loader.
     *
     * @var  ?LoaderInterface
     */
    protected ?LoaderInterface $loader = null;

    /**
     * Property extensions.
     *
     * @var  ExtensionInterface[]
     */
    protected array $extensions = [];

    /**
     * Property debugExtension.
     *
     * @var  ?DebugExtension
     */
    protected ?DebugExtension $debugExtension = null;

    /**
     * render
     *
     * @param  string  $layout
     * @param  array   $data
     *
     * @param  array   $options
     *
     * @return  string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $layout, array $data = [], array $options = []): string
    {
        $layout = pathinfo($layout, PATHINFO_EXTENSION) === 'twig' ? $layout : $layout . '.twig';

        $this->extensions = array_merge($this->extensions, (array) $this->getOption('extensions', []));

        return $this->getEngine([], true)->render($layout, $data);
    }

    /**
     * getLoader
     *
     * @return  LoaderInterface
     */
    public function getLoader(): LoaderInterface
    {
        if (!$this->loader) {
            if ($this->getOption('path_separator')) {
                $this->loader = new TwigFilesystemLoader(
                    iterator_to_array(clone $this->getPaths()),
                    $this->getOption('path_separator')
                );
            } else {
                $this->loader = new TwigFilesystemLoader(iterator_to_array(clone $this->getPaths()));
            }
        }

        return $this->loader;
    }

    /**
     * setLoader
     *
     * @param   LoaderInterface $loader
     *
     * @return  static  Return self to support chaining.
     */
    public function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * addExtension
     *
     * @param ExtensionInterface $extension
     *
     * @return  static
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /**
     * getTwig
     *
     * @param  array  $options
     * @param  bool   $new
     *
     * @return Environment
     */
    public function getEngine(array $options = [], bool $new = false): Environment
    {
        if (!($this->engine instanceof Environment) || $new) {
            $this->engine = new Environment($this->getLoader(), $this->getOptions());

            foreach (GlobalContainer::getExtensions() as $extension) {
                $this->engine->addExtension(clone $extension);
            }

            foreach ($this->extensions as $extension) {
                $this->engine->addExtension($extension);
            }

            foreach (GlobalContainer::getGlobals() as $name => $value) {
                $this->engine->addGlobal($name, $value);
            }

            if ($this->getOption('debug')) {
                $this->engine->addExtension($this->getDebugExtension());
            }
        }

        return $this->engine;
    }

    /**
     * setTwig
     *
     * @param  object|null  $engine
     *
     * @return TwigRenderer Return self to support chaining.
     */
    public function setEngine(?object $engine)
    {
        if (!($engine instanceof Environment)) {
            throw new \InvalidArgumentException('Engine object should be Twig_environment');
        }

        $this->engine = $engine;

        return $this;
    }

    /**
     * Method to get property DebugExtension
     *
     * @return  DebugExtension
     */
    public function getDebugExtension(): DebugExtension
    {
        if (!$this->debugExtension) {
            $this->debugExtension = new DebugExtension();
        }

        return $this->debugExtension;
    }

    /**
     * Method to set property debugExtension
     *
     * @param   ExtensionInterface $debugExtension
     *
     * @return  static  Return self to support chaining.
     */
    public function setDebugExtension(ExtensionInterface $debugExtension)
    {
        $this->debugExtension = $debugExtension;

        return $this;
    }

    /**
     * Method to get property Extensions
     *
     * @return  ExtensionInterface[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * Method to set property extensions
     *
     * @param   ExtensionInterface[] $extensions Twig extenions
     *
     * @return  static  Return self to support chaining.
     */
    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;

        return $this;
    }

    public function reset(): void
    {
        $this->engine = null;
        $this->loader = null;
        $this->extensions = [];
        $this->engine = null;
        $this->debugExtension = null;
    }
}
