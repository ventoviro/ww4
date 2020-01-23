<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver;

use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The AbstractConnection class.
 */
abstract class AbstractConnection implements ConnectionInterface
{
    use OptionAccessTrait;

    /**
     * @var mixed
     */
    protected $connection;

    /**
     * @var array
     */
    protected $defaultOptions = [];

    /**
     * AbstractConnection constructor.
     *
     * @param  array  $options
     */
    public function __construct(array $options)
    {
        $this->prepareOptions(
            $this->defaultOptions,
            $options
        );

        $this->prepare();
    }

    /**
     * isSupported
     *
     * @return  bool
     */
    abstract public static function isSupported(): bool;

    protected function prepare(): void
    {
        //
    }

    abstract public static function getParameters(array $options): array;

    /**
     * connect
     *
     * @return  mixed
     */
    public function connect()
    {
        if ($this->connection) {
            return $this->connection;
        }

        return $this->connection = $this->doConnect(static::getParameters($this->options));
    }

    abstract protected function doConnect(array $options);

    /**
     * disconnect
     *
     * @return  mixed
     */
    abstract public function disconnect();

    /**
     * isConnected
     *
     * @return  bool
     */
    public function isConnected(): bool
    {
        return $this->connection !== null;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->connection;
    }
}
