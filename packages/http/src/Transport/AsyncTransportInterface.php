<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Http\Transport;

use Psr\Http\Message\RequestInterface;
use Windwalker\Promise\PromiseInterface;

/**
 * Interface AsyncTransportInterface
 */
interface AsyncTransportInterface
{
    /**
     * sendRequest
     *
     * @param  RequestInterface  $request
     *
     * @param  array             $options
     *
     * @return  mixed|PromiseInterface
     */
    public function sendRequest(RequestInterface $request, array $options = []);
}
