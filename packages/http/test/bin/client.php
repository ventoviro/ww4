<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Windwalker\Http\Event\ErrorEvent;
use Windwalker\Http\Event\RequestEvent;
use Windwalker\Http\HttpClient;
use Windwalker\Http\Request\ServerRequestFactory;
use Windwalker\Http\Server\HttpServer;
use Windwalker\Http\Server\PhpServer;
use Windwalker\Http\Transport\StreamTransport;
use Windwalker\Promise\Scheduler\ScheduleRunner;

$autoload = __DIR__ . '/../../vendor/autoload.php';

if (!is_file($autoload)) {
    $autoload = __DIR__ . '/../../../../vendor/autoload.php';
}

include $autoload;

$t1 = new StreamTransport();
$fp1 = $t1->createConnection(
    (new \Windwalker\Http\Request\Request())
        ->withRequestTarget('https://google.com')
);

$t2 = new StreamTransport();
$fp2 = $t1->createConnection(
    (new \Windwalker\Http\Request\Request())
        ->withRequestTarget('https://github.com')
);

show($socket = stream_socket_server("tcp://0.0.0.0:6001", $errno, $errstr));
