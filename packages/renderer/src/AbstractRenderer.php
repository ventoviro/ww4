<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Renderer;

use SplPriorityQueue;
use Windwalker\Utilities\Classes\OptionAccessTrait;
use Windwalker\Utilities\Iterator\PriorityQueue;

/**
 * Class AbstractRenderer
 *
 * @since 2.0
 */
abstract class AbstractRenderer implements RendererInterface
{
    use OptionAccessTrait;

    /**
     * Property paths.
     *
     * @var PriorityQueue
     */
    protected PriorityQueue $paths;

    /**
     * Class init.
     *
     * @param  string|array|SplPriorityQueue  $paths
     * @param  array                   $options
     */
    public function __construct(SplPriorityQueue|string|array $paths, array $options = [])
    {
        $this->setPaths($paths);

        $this->prepareOptions(
            [],
            $options
        );
    }

    /**
     * finFile
     *
     * @param  string       $file
     * @param  string|null  $ext
     *
     * @return string|null
     */
    public function findFile(string $file, ?string $ext = null): ?string
    {
        $paths = clone $this->getPaths();

        $file = str_replace('.', '/', $file);

        $ext = $ext ? '.' . trim($ext, '.') : '';

        foreach ($paths as $path) {
            $filePath = $path . '/' . $file . $ext;

            if (is_file($filePath)) {
                return realpath($filePath) ?: null;
            }
        }

        return null;
    }

    /**
     * has
     *
     * @param  string  $file
     * @param  string  $ext
     *
     * @return  bool
     *
     * @since  3.5.2
     */
    public function has(string $file, string $ext = ''): bool
    {
        return $this->findFile($file, $ext) !== null;
    }

    /**
     * getPaths
     *
     * @return  PriorityQueue
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * setPaths
     *
     * @param  array|string|SplPriorityQueue  $paths
     *
     * @return static Return self to support chaining.
     */
    public function setPaths(array|string|SplPriorityQueue $paths)
    {
        if ($paths instanceof SplPriorityQueue) {
            $paths = new PriorityQueue($paths);
        }

        if (!$paths instanceof PriorityQueue) {
            $priority = new PriorityQueue();

            foreach ((array) $paths as $i => $path) {
                $priority->insert($path, 100 - ($i * 10));
            }

            $paths = $priority;
        }

        $this->paths = $paths;

        return $this;
    }

    /**
     * addPath
     *
     * @param  string   $path
     * @param  integer  $priority
     *
     * @return  static
     */
    public function addPath(string $path, int $priority = 100)
    {
        $this->paths->insert($path, $priority);

        return $this;
    }

    /**
     * clearPaths
     *
     * @return  static
     */
    public function clearPaths()
    {
        $this->setPaths([]);

        return $this;
    }

    /**
     * dumpPaths
     *
     * @return  array
     */
    public function dumpPaths(): array
    {
        $paths = clone $this->paths;

        $return = [];

        foreach ($paths as $path) {
            $return[] = $path;
        }

        return $return;
    }
}
