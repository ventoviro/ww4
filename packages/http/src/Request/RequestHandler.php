<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Http\Request;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The RequestHandler class.
 */
class RequestHandler implements RequestHandlerInterface
{
    /**
     * handle
     *
     * @param  ServerRequestInterface  $request
     *
     * @return  ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

    }
}
