<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Data;

use Windwalker\Scalars\ArrayObject;
use Windwalker\Utilities\Arr;

/**
 * The Collection class.
 *
 * @since  __DEPLOY_VERSION__
 */
class Collection extends ArrayObject
{
    use StructureTrait;

    /**
     * Structure constructor.
     *
     * @param  mixed        $data
     * @param  string|null  $format
     * @param  array        $options
     */
    public function __construct($data = [], ?string $format = null, array $options = [])
    {
        parent::__construct([]);

        $this->load($data, $format, $options);
    }

    /**
     * extract
     *
     * @param  string|null  $path
     * @param  bool         $reference
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function extract(?string $path = null, bool $reference = false)
    {
        $new = new static();

        if ((string) $path === '') {
            if ($reference) {
                $new->storage = &$this->storage;
            } else {
                $new->storage = $this->storage;
            }
        } else {
            if ($reference) {
                $new->storage = &Arr::get($this->storage, $path);
            } else {
                $new->storage = Arr::get($this->storage, $path);
            }
        }

        return $new;
    }

    /**
     * proxy
     *
     * @param  string|null  $path
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function proxy(?string $path)
    {
        return $this->extract($path, true);
    }

    /**
     * getDeep
     *
     * @param  string  $path
     * @param  string  $delimiter
     *
     * @return  mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function &getDeep(string $path, string $delimiter = '.')
    {
        return Arr::get($this->storage, $path, $delimiter);
    }

    /**
     * setDeep
     *
     * @param  string  $path
     * @param  mixed   $value
     * @param  string  $delimiter
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setDeep(string $path, $value, string $delimiter = '.')
    {
        $this->storage = Arr::set($this->storage, $path, $value, $delimiter);

        return $this;
    }

    /**
     * withDeep
     *
     * @param  string  $path
     * @param  mixed   $value
     * @param  string  $delimiter
     *
     * @return  Collection
     *
     * @since  __DEPLOY_VERSION__
     */
    public function withDeep(string $path, $value, string $delimiter = '.')
    {
        $new = clone $this;

        $new->storage = Arr::set($new->storage, $path, $value, $delimiter);

        return $new;
    }

    /**
     * hasDeep
     *
     * @param  string  $path
     *
     * @return  bool
     *
     * @since  __DEPLOY_VERSION__
     */
    public function hasDeep(string $path): bool
    {
        return Arr::has($this->storage, $path);
    }

    /**
     * removeDeep
     *
     * @param  string  $path
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function removeDeep(string $path)
    {
        $this->storage = Arr::remove($this->storage, $path);

        return $this;
    }

    /**
     * withRemoveDeep
     *
     * @param  string  $path
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function withRemoveDeep(string $path)
    {
        $new = clone $this;

        $new->storage = Arr::remove($new->storage, $path);

        return $new;
    }
}
