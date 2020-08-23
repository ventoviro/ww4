<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Renderer\Twig;

use Twig\Loader\FilesystemLoader;

/**
 * The TwigFilesystemLoader class.
 *
 * @since  2.1.1
 */
class TwigFilesystemLoader extends FilesystemLoader
{
    /**
     * Property separator.
     *
     * @var  string
     */
    protected $separator;

    /**
     * TwigFilesystemLoader constructor.
     *
     * @param array|string $paths
     * @param string       $separator
     */
    public function __construct($paths, $separator = '.')
    {
        $this->separator = $separator;

        parent::__construct($paths);
    }

    /**
     * Method to get property Separator
     *
     * @return  string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Method to set property separator
     *
     * @param   string $separator
     *
     * @return  static  Return self to support chaining.
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function findTemplate($name, $throw = true)
    {
        $name = $this->normalizeName($name);

        return parent::findTemplate($name, $throw);
    }

    /**
     * normalizeName
     *
     * @param   string $name
     *
     * @return  string
     */
    protected function normalizeName($name)
    {
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        if ($ext === 'twig') {
            $name = substr($name, 0, -5);
        }

        $name = str_replace($this->separator, '/', $name);

        return $name . '.twig';
    }

    /**
     * Adds a path where templates are stored.
     *
     * @param string $path      A path where to look for templates
     * @param string $namespace A path name
     */
    public function addPath($path, $namespace = self::MAIN_NAMESPACE)
    {
        // invalidate the cache
        $this->cache = [];

        $this->paths[$namespace][] = rtrim($path, '/\\');
    }
}
