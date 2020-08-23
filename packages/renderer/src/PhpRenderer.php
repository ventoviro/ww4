<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Renderer;

use Windwalker\Utilities\Wrapper\RawWrapper;

/**
 * Class PhpRenderer
 *
 * @since 2.0
 */
class PhpRenderer extends AbstractRenderer
{
    /**
     * Property block.
     *
     * @var  array
     */
    protected array $block = [];

    /**
     * Property blockQueue.
     *
     * @var  \SplStack
     */
    protected ?\SplStack $blockQueue = null;

    /**
     * Property currentBlock.
     *
     * @var  string
     */
    protected ?string $currentBlock = null;

    /**
     * Property extends.
     *
     * @var  string
     */
    protected ?string $extend = null;

    /**
     * Property parent.
     *
     * @var  PhpRenderer
     */
    protected ?PhpRenderer $parent = null;

    /**
     * Property data.
     *
     * @var  array
     */
    protected ?array $data = null;

    /**
     * Property file.
     *
     * @var string
     */
    protected ?string $file = null;

    /**
     * render
     *
     * @param  string  $layout
     * @param  array   $data
     *
     * @param  array   $options
     *
     * @return  string
     * @throws \UnexpectedValueException
     */
    public function render(string $layout, array $data = [], array $options = []): string
    {
        $this->data = $data = (array) $data;

        $this->prepareData($data);

        $__filePath = $this->findFile($layout);

        if (!$__filePath) {
            $__paths = $this->dumpPaths();

            $__paths = "\n " . implode(" |\n ", $__paths);

            throw new \UnexpectedValueException(sprintf('File: %s not found. Paths in queue: %s', $layout, $__paths));
        }

        foreach ($data as $key => $value) {
            if ($key === 'data') {
                $key = '_data';
            }

            $$key = $value;
        }

        unset($data);

        // Start an output buffer.
        ob_start();

        // Load the layout.
        include $__filePath;

        // Get the layout contents.
        $output = ob_get_clean();

        // Handler extend
        if (!$this->extend) {
            return $output;
        }

        /** @var $parent phpRenderer */
        $parent = $this->createSelf();

        foreach ($this->block as $name => $block) {
            $parent->setBlock($name, $block);
        }

        $output = $parent->render($this->extend, $this->data, $options);

        return $output;
    }

    /**
     * Method to escape output.
     *
     * @param  mixed  $output  The output to escape.
     *
     * @return  string  The escaped output.
     *
     * @since   2.0
     */
    public function escape($output): string
    {
        if ($output instanceof RawWrapper) {
            return $output->get();
        }

        // Escape the output.
        return htmlspecialchars((string) $output, ENT_COMPAT, 'UTF-8');
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
        $ext ??= $this->getOption('file_ext', '.php');

        return parent::findFile($file, $ext);
    }

    /**
     * load
     *
     * @param  string  $file
     * @param  array   $data
     *
     * @return  string
     */
    public function load(string $file, array $data = []): string
    {
        $data = array_merge($this->data, (array) $data);

        $renderer = $this->createSelf();

        return $renderer->render($file, $data);
    }

    /**
     * prepareData
     *
     * @param  array &$data
     *
     * @return void
     */
    protected function prepareData(array &$data = []): void
    {
    }

    /**
     * getParent
     *
     * @return  mixed|null
     */
    public function parent()
    {
        if (!$this->extend) {
            return null;
        }

        if (!$this->parent) {
            $this->parent = $this->createSelf();

            $this->parent->render($this->extend, $this->data);
        }

        return $this->parent->getBlock($this->currentBlock);
    }

    /**
     * createSelf
     *
     * @return  static
     */
    protected function createSelf()
    {
        return new static($this->paths, $this->getOptions());
    }

    /**
     * extend
     *
     * @param  string  $name
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function extend(string $name): void
    {
        if ($this->extend) {
            throw new \LogicException('Please just extend one file.');
        }

        $this->extend = $name;
    }

    /**
     * getBlock
     *
     * @param  string  $name
     *
     * @return mixed|null
     */
    public function getBlock(string $name)
    {
        return !empty($this->block[$name]) ? $this->block[$name] : null;
    }

    /**
     * setBlock
     *
     * @param  string  $name
     * @param  string  $content
     *
     * @return  PhpRenderer  Return self to support chaining.
     */
    public function setBlock(string $name, string $content = ''): PhpRenderer
    {
        $this->block[$name] = $content;

        return $this;
    }

    /**
     * setBlock
     *
     * @param  string  $name
     *
     * @return void
     */
    public function block(string $name): void
    {
        $this->currentBlock = $name;

        $this->getBlockQueue()->push($name);

        // Start an output buffer.
        ob_start();
    }

    /**
     * endblock
     *
     * @return  void
     */
    public function endblock(): void
    {
        $name = $this->getBlockQueue()->pop();

        // If this block name not exists on parent level, we just echo inner content.
        if (!empty($this->block[$name])) {
            ob_get_clean();

            echo $this->block[$name];

            return;
        }

        // Get the layout contents.
        echo $this->block[$name] = ob_get_clean();
    }

    /**
     * getBlockQueue
     *
     * @return  \SplStack
     */
    public function getBlockQueue(): \SplStack
    {
        if (!$this->blockQueue) {
            $this->blockQueue = new \SplStack();
        }

        return $this->blockQueue;
    }

    /**
     * reset
     *
     * @return  void
     */
    public function reset(): void
    {
        $this->file = null;
        $this->extend = null;
        $this->parent = null;
        $this->data = null;
        $this->block = [];
        $this->blockQueue = null;
        $this->currentBlock = null;
    }
}
