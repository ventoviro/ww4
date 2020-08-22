<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Http\Server;

/**
 * The AdaptableServer class.
 */
abstract class AdaptableServer extends AbstractServer
{
    protected ServerInterface $adapter;

    /**
     * AdaptableServer constructor.
     *
     * @param  ServerInterface|null  $adapter
     */
    public function __construct(?ServerInterface $adapter = null)
    {
        $this->adapter = $adapter ?? new PhpServer();
    }

    /**
     * @return ServerInterface
     */
    public function getAdapter(): ServerInterface
    {
        return $this->adapter;
    }

    /**
     * @param  ServerInterface  $adapter
     *
     * @return  static  Return self to support chaining.
     */
    public function setAdapter(ServerInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}
