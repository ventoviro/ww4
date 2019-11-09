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
     * @param  mixed   $key
     * @param  mixed   $value
     * @param  string  $delimiter
     *
     * @return static
     */
    public function set($key, $value, $delimiter = '.')
    {
        Arr::set($key, $value, $delimiter);

        return $this;
    }

    /**
     * Set value and immutable.
     *
     * @param  mixed   $key
     * @param  mixed   $value
     * @param  string  $delimiter
     *
     * @return  $this
     *
     * @since  __DEPLOY_VERSION__
     */
    public function with($key, $value, $delimiter = '.'): Structure
    {
        $new = clone $this;

        Arr::set($key, $value, $delimiter);

        return $new;
    }

    /**
     * Set value default if not exists.
     *
     * @param  mixed   $key
     * @param  mixed   $default
     * @param  string  $delimiter
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function def($key, $default, $delimiter = '.')
    {
        Arr::def($this->storage, $key, $default);

        return $this;
    }

    /**
     * withDef
     *
     * @param  mixed   $key
     * @param  mixed   $default
     * @param  string  $delimiter
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function withDef($key, $default, $delimiter = '.')
    {
        $new = clone $this;

        Arr::def($new->storage, $key, $default, $delimiter);

        return $new;
    }

    /**
     * Check a key exists or not.
     *
     * @param  mixed   $key
     * @param  string  $delimiter
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function has($key, $delimiter = '.'): bool
    {
        return Arr::has($this->storage, $key, $delimiter);
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
    public function setFormatRegistry(FormatRegistry $formatRegistry)
    {
        $this->formatRegistry = $formatRegistry;

        return $this;
    }
}
