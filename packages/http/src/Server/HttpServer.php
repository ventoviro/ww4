<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later.
 */

declare(strict_types=1);

namespace Windwalker\Http\Server;

use Psr\Http\Message\ServerRequestInterface;
use Windwalker\Event\EventListenableTrait;

/**
 * The Server class.
 *
 * @since  3.0
 */
class HttpServer implements ServerInterface
{
    use EventListenableTrait;

    protected int $options;

    /**
     * @var ServerAdapterInterface|null
     */
    protected ?ServerAdapterInterface $adapter;

    /**
     * Create a Server instance from an existing request object
     *
     * Provided a callback, an existing request object, and optionally an
     * existing response object, create and return the Server instance.
     *
     * If no Response object is provided, one will be created.
     *
     * @param  int                          $options
     * @param  ServerAdapterInterface|null  $adapter
     */
    public function __construct(
        ServerAdapterInterface $adapter = null,
        int $options = 0
    ) {
        $this->options = $options;
        $this->adapter = $adapter ?? new PhpAdapter();

        $this->adapter->getDispatcher()
            ->registerDealer($this->getDispatcher());
    }

    public function handle(?ServerRequestInterface $request = null)
    {
        $this->adapter->handle($request);
    }

    /**
     * Execute the server.
     *
     * @param  string  $host
     * @param  int     $port
     * @param  int     $options
     */
    public function listen(string $host = '0.0.0.0', int $port = 0, int $options = 0): void
    {
        $this->adapter->resume();
    }

    public function stop(): void
    {
        $this->adapter->close();
    }
}
