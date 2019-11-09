<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Data;

use Windwalker\Data\Format\FormatRegistry;
use Windwalker\Scalars\ArrayObject;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\TypeCast;

/**
 * The Structure class.
 *
 * @since  __DEPLOY_VERSION__
 */
class Structure extends Collection
{
    protected FormatRegistry $formatRegistry;

    /**
     * Structure constructor.
     *
     * @param  mixed        $data
     * @param  string|null  $format
     * @param  array        $options
     */
    public function __construct($data, ?string $format = null, array $options = [])
    {
        $registry = $this->getFormatRegistry();

        if (is_array($data) || is_object($data)) {
            $storage = TypeCast::toArray($data, $options['to_array'] ?? true);
        } else {
            $storage = $registry->load($data, $format, $options);
        }

        parent::__construct($storage);
    }

    /**
     * Get value from this object.
     *
     * @param  string  $key
     * @param  string  $delimiter
     *
     * @return mixed
     */
    public function get($key, $delimiter = '.')
    {
        return Arr::get($this->storage, $key, $delimiter);
    }

    /**
     * Set value to this object.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return  static
     */
    public function set($key, $value)
    {
    }

    /**
     * Set value and immutable.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return  $this
     *
     * @since  __DEPLOY_VERSION__
     */
    public function with($key, $value): Structure
    {
        $new = clone $this;

        $new->storage[$key] = $value;

        return $new;
    }

    /**
     * Set value default if not exists.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function def($key, $default)
    {
        Arr::def($this->storage, $key, $default);

        return $this;
    }

    /**
     * Check a key exists or not.
     *
     * @param  mixed  $key
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function has($key): bool
    {
    }

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
    public function setFormatRegistry(FormatRegistry $formatRegistry): self
    {
        $this->formatRegistry = $formatRegistry;

        return $this;
    }
}
