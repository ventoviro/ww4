<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Session\Handler;

use Predis\Response\ErrorInterface;
use Windwalker\Utilities\Assert\ArgumentsAssert;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The RedisHandler class.
 */
class RedisHandler extends AbstractHandler
{
    use OptionAccessTrait;

    /**
     * @var \Redis|\RedisArray|\Predis\ClientInterface
     */
    protected $driver;

    /**
     * RedisHandler constructor.
     *
     * @param  \Predis\ClientInterface|\Redis|\RedisArray|RedisCaster  $driver
     */
    public function __construct($driver, array $options = [])
    {
        ArgumentsAssert::assert(
            $driver instanceof \Redis
            || $driver instanceof \RedisArray
            || $driver instanceof \Predis\ClientInterface,
            '{caller} argument 1 should be Redis instance, %s given.',
            $driver
        );

        $this->prepareOptions(
            [
                'prefix' => 'ww_sess_',
                'ttl' => null
            ],
            $options
        );

        $this->driver = $driver;
    }

    public function getKey(string $id)
    {
        $key = $id;

        if ($this->getOption('prefix')) {
            $key = $this->getOption('prefix') . $id;
        }

        return $key;
    }

    /**
     * doRead
     *
     * @param  string  $id
     *
     * @return  string|null
     */
    protected function doRead(string $id): ?string
    {
        $r = $this->getDriver()->get($this->getKey($id));

        if ($r === false || $r === null) {
            return null;
        }

        return (string) $r;
    }

    /**
     * isSupported
     *
     * @return  bool
     */
    public static function isSupported(): bool
    {
        return true;
    }

    /**
     * destroy
     *
     * @param  string  $id
     *
     * @return  bool
     */
    public function destroy($id)
    {
        $this->getDriver()->del($this->getKey($id));

        return true;
    }

    /**
     * gc
     *
     * @param  int  $maxlifetime
     *
     * @return  bool
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * write
     *
     * @param  string  $id
     * @param  string  $data
     *
     * @return  bool
     */
    public function write($id, $data)
    {
        $result = $this->getDriver()
            ->setEx(
                $this->getKey($id),
                (int) ($this->ttl ?? $this->getOption('gc_maxlifetime') ?? ini_get('session.gc_maxlifetime')),
                $data
            );

        return $result && !$result instanceof ErrorInterface;
    }

    /**
     * updateTimestamp
     *
     * @param  string  $id
     * @param  string  $data
     *
     * @return  bool
     */
    public function updateTimestamp($id, $data)
    {
        return (bool) $this->getDriver()
            ->expire(
                $this->getKey($id),
                (int) ($this->ttl ?? $this->getOption('gc_maxlifetime') ?? ini_get('session.gc_maxlifetime'))
            );

    }

    /**
     * @return \Predis\ClientInterface|\Redis|\RedisArray
     */
    public function getDriver()
    {
        return $this->driver;
    }
}
