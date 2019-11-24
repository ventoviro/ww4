<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Data;

use Windwalker\Data\Format\FormatRegistry;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\TypeCast;

/**
 * The Structure class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait StructureTrait
{
    /**
     * @var FormatRegistry
     */
    protected $formatRegistry;

    /**
     * Method to get property FormatRegistry
     *
     * @return  FormatRegistry
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getFormatRegistry(): FormatRegistry
    {
        if (!$this->formatRegistry) {
            $this->formatRegistry = new FormatRegistry();
        }

        return $this->formatRegistry;
    }

    /**
     * Method to set property formatRegistry
     *
     * @param  FormatRegistry  $formatRegistry
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setFormatRegistry(FormatRegistry $formatRegistry)
    {
        $this->formatRegistry = $formatRegistry;

        return $this;
    }

    /**
     * load
     *
     * @param  mixed   $data
     * @param  string  $format
     * @param  array   $options
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function load($data, string $format = 'json', array $options = [])
    {
        $this->storage = Arr::mergeRecursive($this->storage, $this->loadData($data, $format, $options));

        return $this;
    }

    /**
     * withLoad
     *
     * @param  mixed   $data
     * @param  string  $format
     * @param  array   $options
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function withLoad($data, string $format = 'json', array $options = [])
    {
        $new = clone $this;

        $new->storage = Arr::mergeRecursive($new->storage, $this->loadData($data, $format, $options));

        return $new;
    }

    /**
     * loadData
     *
     * @param  mixed   $data
     * @param  string  $format
     * @param  array   $options
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    protected function loadData($data, string $format = 'json', array $options = []): array
    {
        if (is_array($data) || is_object($data)) {
            $storage = TypeCast::toArray($data, $options['to_array'] ?? true);
        } else {
            $registry = $this->getFormatRegistry();

            $storage = $registry->load((string) $data, $format, $options);
        }

        return $storage;
    }

    /**
     * toString
     *
     * @param  string  $format
     * @param  array   $options
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public function toString(string $format = 'json', array $options = []): string
    {
        return $this->getFormatRegistry()->dump($this->storage, $format, $options);
    }

    /**
     * __toString
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
