<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Data;

use Windwalker\Scalars\ArrayObject;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Assert\TypeAssert;
use Windwalker\Utilities\TypeCast;

/**
 * The Collection class.
 *
 * @method string toJson(array $options = [])
 * @method string toXml(array $options = [])
 * @method string toIni(array $options = [])
 * @method string toPhpString(array $options = [])
 * @method string toYaml(array $options = [])
 * @method string toHjson(array $options = [])
 * @method string toToml(array $options = [])
 *
 * @since  __DEPLOY_VERSION__
 */
class Collection extends ArrayObject
{
    use StructureTrait;

    protected bool $isProxy = false;

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
        } elseif ($reference) {
            $new->storage = &Arr::get($this->storage, $path);
        } else {
            $new->storage = Arr::get($this->storage, $path);
        }

        TypeAssert::assert(
            !($reference && !is_array($new->storage)),
            'Method: {caller} Proxy to sub element should be array, got %s.',
            $new->storage
        );

        TypeAssert::assert(
            is_array($new->storage) || is_object($new->storage) || $new->storage === null,
            'Method: {caller} extract to sub element should be array, object or NULL, got %s.',
            $new->storage
        );

        // Force object to array
        if (!$reference) {
            $new->storage = TypeCast::toArray($new->storage);
        }

        if ($reference) {
            $this->isProxy = true;
        }

        return $new;
    }

    /**
     * proxy
     *
     * @param  string  $path
     *
     * @return  static
     *
     * @since  __DEPLOY_VERSION__
     */
    public function proxy(string $path)
    {
        return $this->extract($path, true);
    }

    /**
     * proxyMap
     *
     * @param  string|null  $column
     *
     * @return  MapProxy|Collection
     */
    public function mapProxy(?string $column = null): MapProxy
    {
        return new MapProxy($this, $column);
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
     * @return  static
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

    /**
     * @return bool
     */
    public function isProxy(): bool
    {
        return $this->isProxy;
    }

    /**
     * __call
     *
     * @param  string  $name
     * @param  array   $args
     *
     * @return  mixed|string
     *
     * @since  __DEPLOY_VERSION__
     */
    public function __call($name, $args)
    {
        $allowFormat = [
            'tojson' => 'json',
            'toxml' => 'xml',
            'toyaml' => 'yaml',
            'tohjson' => 'jhson',
            'toini' => 'ini',
            'tophpstring' => 'php',
            'totoml' => 'json',
        ];

        $method = strtolower($name);

        if ($allowFormat[$method] ?? null) {
            return $this->toString($allowFormat[$method], ...$args);
        }

        return parent::__call($name, $args);
    }
}
