<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Http\Event;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Windwalker\Event\AbstractEvent;

/**
 * The WebRequestEvent class.
 */
class RequestEvent extends AbstractEvent
{
    public ServerRequestInterface $request;

    public ?ResponseInterface $response = null;

    public int $id = 0;

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param  ResponseInterface  $response
     *
     * @return  static  Return self to support chaining.
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @param  ServerRequestInterface  $request
     *
     * @return  static  Return self to support chaining.
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param  int  $id
     *
     * @return  static  Return self to support chaining.
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }
}
