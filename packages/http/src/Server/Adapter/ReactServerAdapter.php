<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Http\Server\Adapter;

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory as ReactFactory;
use React\EventLoop\LoopInterface;
use React\Http\Server;
use React\Socket\Server as SocketServer;
use React\Socket\ServerInterface;
use Windwalker\Event\Event;
use Windwalker\Event\EventListenableTrait;
use Windwalker\Http\Event\ErrorEvent;
use Windwalker\Http\Event\WebRequestEvent;
use Windwalker\Http\Helper\ResponseHelper;
use Windwalker\Http\HttpFactory;
use Windwalker\Http\Response\Response;

/**
 * The ReactServerAdapter class.
 */
class ReactServerAdapter implements ServerAdapterInterface
{
    use EventListenableTrait;

    protected string $host;

    protected int $port;

    protected bool $listening = false;

    protected int $options;

    protected ?LoopInterface $loop = null;

    /**
     * @var ServerInterface|null
     */
    protected ?ServerInterface $socket = null;

    protected ?Server $server = null;

    /**
     * ReactServerAdapter constructor.
     *
     * @param  ServerInterface|null  $server
     * @param  string                $host
     * @param  int                   $port
     * @param  int                   $options
     */
    public function __construct(string $host = '0.0.0.0', int $port = 0, int $options = 0)
    {
        $this->host = $host;
        $this->port = $port;
        $this->options = $options;
        $this->httpFactory = new HttpFactory();
    }

    /**
     * @return bool
     */
    public function isListening(): bool
    {
        return $this->listening;
    }

    protected function prepareServerLoop(): LoopInterface
    {
        $server = $this->getServer();

        $server->listen($this->getSocket());

        return $this->getLoop();
    }

    protected function listen(): void
    {
        if (!$this->listening) {
            $loop = $this->prepareServerLoop();

            $this->listening = true;

            $loop->run();
        }
    }

    public function handle(?ServerRequestInterface $request = null): void
    {
        $this->listen();
    }

    public function resume(): void
    {
        $this->listen();
    }

    public function pause(): void
    {
        $this->getSocket()->pause();
    }

    public function close(): void
    {
        $this->getSocket()->close();
        $this->getLoop()->stop();
    }

    /**
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface
    {
        return $this->loop ??= ReactFactory::create();
    }

    /**
     * @param  LoopInterface|null  $loop
     *
     * @return  static  Return self to support chaining.
     */
    public function setLoop(?LoopInterface $loop)
    {
        $this->loop = $loop;

        return $this;
    }

    /**
     * @return ServerInterface
     */
    public function getSocket(): ServerInterface
    {
        return $this->socket ??= new SocketServer($this->host . ':' . $this->port, $this->getLoop());
    }

    /**
     * @param  ServerInterface|null  $socket
     *
     * @return  static  Return self to support chaining.
     */
    public function setSocket(?ServerInterface $socket)
    {
        $this->socket = $socket;

        return $this;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server ??= $this->createServer();
    }

    protected function createServer(): Server
    {
        $server = new Server(
            $this->getLoop(),
            function (ServerRequestInterface $req) {
                try {
                    $event = $this->emit(
                        WebRequestEvent::wrap('request')
                            ->setRequest($req)
                    );
                } catch (\Throwable $e) {
                    $code = $e->getCode();
                    $code = ResponseHelper::isClientError($code) ? $code : 500;

                    $res = (new HttpFactory())->createResponse($code);
                    $res->getBody()->write((string) $e);

                    return $res;
                }

                return $event->getResponse();
            }
        );

        $server->on('error', function (\Throwable $e) {
            $event = $this->emit(
                ErrorEvent::wrap('error')
                    ->setException($e)
            );
        });

        return $server;
    }

    /**
     * @param  Server|null  $server
     *
     * @return  static  Return self to support chaining.
     */
    public function setServer(?Server $server)
    {
        $this->server = $server;

        return $this;
    }
}
