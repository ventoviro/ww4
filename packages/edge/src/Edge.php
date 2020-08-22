<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Edge;

use Closure;
use Exception;
use Throwable;
use Windwalker\Edge\Cache\EdgeArrayCache;
use Windwalker\Edge\Cache\EdgeCacheInterface;
use Windwalker\Edge\Cache\EdgeFileCache;
use Windwalker\Edge\Compiler\EdgeCompiler;
use Windwalker\Edge\Compiler\EdgeCompilerInterface;
use Windwalker\Edge\Concern\ManageComponentTrait;
use Windwalker\Edge\Concern\ManageEventTrait;
use Windwalker\Edge\Concern\ManageLayoutTrait;
use Windwalker\Edge\Concern\ManageStackTrait;
use Windwalker\Edge\Exception\EdgeException;
use Windwalker\Edge\Extension\EdgeExtensionInterface;
use Windwalker\Edge\Loader\EdgeLoaderInterface;
use Windwalker\Edge\Loader\EdgeStringLoader;
use Windwalker\Utilities\Arr;

/**
 * The Edge template engine.
 *
 * This is a modified version of Laravel Blade engine.
 *
 * @see    https://github.com/illuminate/view/blob/master/Factory.php
 *
 * @since  3.0
 */
class Edge
{
    use ManageComponentTrait;
    use ManageEventTrait;
    use ManageLayoutTrait;
    use ManageStackTrait;

    /**
     * Property globals.
     *
     * @var  array
     */
    protected array $globals = [];

    /**
     * Property extensions.
     *
     * @var  EdgeExtensionInterface[]
     */
    protected array $extensions = [];

    /**
     * Property sections.
     *
     * @var  array
     */
    protected array $sections;

    /**
     * @var array
     */
    protected array $hasParents = [];

    /**
     * The number of active rendering operations.
     *
     * @var int
     */
    protected int $renderCount = 0;

    /**
     * Property pushes.
     *
     * @var array
     */
    protected array $pushes = [];

    /**
     * Property loader.
     *
     * @var  EdgeLoaderInterface
     */
    protected ?EdgeLoaderInterface $loader = null;

    /**
     * Property compiler.
     *
     * @var  ?EdgeCompilerInterface
     */
    protected ?EdgeCompilerInterface $compiler = null;

    /**
     * Property cacheHandler.
     *
     * @var  EdgeCacheInterface
     */
    protected ?EdgeCacheInterface $cache = null;

    protected ?object $context = null;

    /**
     * EdgeEnvironment constructor.
     *
     * @param EdgeLoaderInterface   $loader
     * @param EdgeCompilerInterface $compiler
     * @param EdgeCacheInterface    $cache
     */
    public function __construct(
        EdgeLoaderInterface $loader = null,
        EdgeCacheInterface $cache = null,
        EdgeCompilerInterface $compiler = null
    ) {
        $this->loader = $loader ?: new EdgeStringLoader();
        $this->compiler = $compiler ?: new EdgeCompiler();
        $this->cache = $cache ?: new EdgeArrayCache();
    }

    public function renderWithContext(string $layout, array $data = [], ?object $context = null): string
    {
        $this->context = $context;

        $result = $this->render($layout, $data);

        $this->context = null;

        return $result;
    }

    /**
     * render
     *
     * @param  string  $__layout
     * @param  array   $__data
     * @param  array   $__more
     *
     * @return string
     * @throws EdgeException
     */
    public function render(string $__layout, array $__data = [], array $__more = [])
    {
        // TODO: Aliases

        $this->incrementRender();

        $__path = $this->loader->find($__layout);

        if ($this->cache->isExpired($__path)) {
            $compiler = $this->prepareExtensions(clone $this->compiler);

            $compiled = $compiler->compile($this->loader->load($__path));

            $this->cache->store($__path, $compiled);

            unset($compiler, $compiled);
        }

        $__data = array_merge($this->getGlobals(true), $__more, $__data);

        unset($__data['__path'], $__data['__data']);

        $closure = $this->getRenderFunction($__data);

        if ($this->getContext()) {
            $closure = $closure->bindTo($this->getContext(), $this->getContext());
        }

        ob_start();

        try {
            $closure($__path);
        } catch (Throwable $e) {
            ob_clean();
            $this->wrapException($e, $__path, $__layout);

            return null;
        }

        $result = ltrim(ob_get_clean());

        $this->decrementRender();

        $this->flushSectionsIfDoneRendering();

        return $result;
    }

    protected function getRenderFunction(array $data): Closure
    {
        $__data = $data;
        $__edge = $this;

        return function ($__path) use ($__data, $__edge) {
            extract($__data, EXTR_OVERWRITE);

            if ($__edge->getCache() instanceof EdgeFileCache) {
                include $__edge->getCache()->getCacheFile($__edge->getCache()->getCacheKey($__path));
            } else {
                eval(' ?>' . $__edge->getCache()->load($__path) . '<?php ');
            }
        };
    }

    /**
     * wrapException
     *
     * @param  Throwable  $e
     * @param  string     $path
     * @param  string     $layout
     *
     * @return  void
     *
     * @throws EdgeException
     */
    protected function wrapException(\Throwable $e, string $path, string $layout)
    {
        $msg = $e->getMessage();

        $msg .= sprintf("\n\n| View layout: %s (%s)", $path, $layout);

        $cache = $this->getCache();

        if ($cache instanceof EdgeFileCache) {
            if (str_starts_with(realpath($cache->getPath()), $e->getFile())) {
                throw new EdgeException($msg, $e->getCode(), $path, $e->getLine());
            }
        }

        throw new EdgeException($msg, $e->getCode(), null, null, $e);
    }

    /**
     * @return object|null
     */
    public function getContext(): ?object
    {
        return $this->context;
    }

    /**
     * @param  object|null  $context
     *
     * @return  static  Return self to support chaining.
     */
    public function setContext(?object $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Normalize a view name.
     *
     * @param  string $name
     *
     * @return string
     */
    protected function normalizeName($name)
    {
        // TODO: Handle namespace

        return str_replace('/', '.', $name);
    }

    /**
     * escape
     *
     * @param  string $string
     *
     * @return  string
     */
    public function escape($string)
    {
        return htmlspecialchars((string) $string, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Get the rendered contents of a partial from a loop.
     *
     * @param  string $view
     * @param  array  $data
     * @param  string $iterator
     * @param  string $empty
     *
     * @return string
     * @throws EdgeException
     */
    public function renderEach(string $view, array $data, string $iterator, string $empty = 'raw|')
    {
        $result = '';

        // If is actually data in the array, we will loop through the data and append
        // an instance of the partial view to the final result HTML passing in the
        // iterated value of this data array, allowing the views to access them.
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $data = ['key' => $key, $iterator => $value];

                $result .= $this->render($view, $data);
            }
        } elseif (str_starts_with($empty, 'raw|')) {
            // If there is no data in the array, we will render the contents of the empty
            // view. Alternatively, the "empty view" could be a raw string that begins
            // with "raw|" for convenience and to let this know that it is a string.
            $result = substr($empty, 4);
        } else {
            $result = $this->render($empty);
        }

        return $result;
    }



    /**
     * Increment the rendering counter.
     *
     * @return void
     */
    public function incrementRender()
    {
        $this->renderCount++;
    }

    /**
     * Decrement the rendering counter.
     *
     * @return void
     */
    public function decrementRender()
    {
        $this->renderCount--;
    }

    /**
     * Check if there are no active render operations.
     *
     * @return bool
     */
    public function doneRendering()
    {
        return $this->renderCount == 0;
    }

    /**
     * prepareDirectives
     *
     * @param EdgeCompilerInterface $compiler
     *
     * @return EdgeCompilerInterface
     */
    public function prepareExtensions(EdgeCompilerInterface $compiler)
    {
        foreach ($this->getExtensions() as $extension) {
            foreach ((array) $extension->getDirectives() as $name => $directive) {
                $compiler->directive($name, $directive);
            }

            foreach ((array) $extension->getParsers() as $parser) {
                $compiler->parser($parser);
            }
        }

        return $compiler;
    }

    /**
     * arrayExcept
     *
     * @param array $array
     * @param array $fields
     *
     * @return  array
     */
    public function except(array $array, array $fields)
    {
        return Arr::except($array, $fields);
    }

    /**
     * Method to get property Globals
     *
     * @param bool $withExtensions
     *
     * @return array
     */
    public function getGlobals($withExtensions = false)
    {
        $globals = $this->globals;

        if ($withExtensions) {
            $temp = [];

            foreach ((array) $this->getExtensions() as $extension) {
                $temp = array_merge($temp, (array) $extension->getGlobals());
            }

            $globals = array_merge($temp, $globals);
        }

        return $globals;
    }

    /**
     * addGlobal
     *
     * @param   string $name
     * @param   string $value
     *
     * @return  static
     */
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;

        return $this;
    }

    public function removeGlobal($name)
    {
        unset($this->globals[$name]);

        return $this;
    }

    public function getGlobal($name, $default = null)
    {
        if (array_key_exists($name, $this->globals)) {
            return $this->globals[$name];
        }

        return $default;
    }

    /**
     * Method to set property globals
     *
     * @param   array $globals
     *
     * @return  static  Return self to support chaining.
     */
    public function setGlobals($globals)
    {
        $this->globals = $globals;

        return $this;
    }

    /**
     * Method to get property Compiler
     *
     * @return  EdgeCompilerInterface
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * Method to set property compiler
     *
     * @param   EdgeCompilerInterface $compiler
     *
     * @return  static  Return self to support chaining.
     */
    public function setCompiler(EdgeCompilerInterface $compiler)
    {
        $this->compiler = $compiler;

        return $this;
    }

    /**
     * Method to get property Loader
     *
     * @return  EdgeLoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Method to set property loader
     *
     * @param   EdgeLoaderInterface $loader
     *
     * @return  static  Return self to support chaining.
     */
    public function setLoader(EdgeLoaderInterface $loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * addExtension
     *
     * @param  EdgeExtensionInterface  $extension
     * @param  string|null             $name
     *
     * @return static
     */
    public function addExtension(EdgeExtensionInterface $extension, ?string $name = null)
    {
        if (!$name) {
            $name = $extension->getName();
        }

        $this->extensions[$name] = $extension;

        return $this;
    }

    /**
     * removeExtension
     *
     * @param   string $name
     *
     * @return  static
     */
    public function removeExtension(string $name)
    {
        if (array_key_exists($name, $this->extensions)) {
            unset($this->extensions[$name]);
        }

        return $this;
    }

    /**
     * hasExtension
     *
     * @param   string $name
     *
     * @return  bool
     */
    public function hasExtension(string $name)
    {
        return array_key_exists($name, $this->extensions) && $this->extensions[$name] instanceof EdgeExtensionInterface;
    }

    /**
     * getExtension
     *
     * @param   string $name
     *
     * @return  EdgeExtensionInterface
     */
    public function getExtension(string $name)
    {
        if ($this->hasExtension($name)) {
            return $this->extensions[$name];
        }

        return null;
    }

    /**
     * Method to get property Extensions
     *
     * @return  Extension\EdgeExtensionInterface[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * Method to set property extensions
     *
     * @param   Extension\EdgeExtensionInterface[] $extensions
     *
     * @return  static  Return self to support chaining.
     */
    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;

        return $this;
    }

    /**
     * Method to get property Cache
     *
     * @return  EdgeCacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Method to set property cache
     *
     * @param   EdgeCacheInterface $cache
     *
     * @return  static  Return self to support chaining.
     */
    public function setCache(EdgeCacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }
}
