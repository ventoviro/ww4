<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

$autoload = __DIR__ . '/../../vendor/autoload.php';

if (!is_file($autoload)) {
    $autoload = __DIR__ . '/../../../../vendor/autoload.php';
}

include $autoload;

use Windwalker\Http\Event\ErrorEvent;
use Windwalker\Http\Event\WebRequestEvent;
use Windwalker\Http\Request\ServerRequestFactory;
use Windwalker\Http\Server\HttpServer;

$server = new HttpServer();
$server->on(
    'request',
    static function (WebRequestEvent $event) {
        $app = require __DIR__ . '/app.php';

        $res = $app($event->getRequest(), $event->getResponse());

        $event->setResponse($res);
    }
);
$server->on(
    'error',
    static function (ErrorEvent $event) {
        echo $event->getException();
    }
);
$server->handle(ServerRequestFactory::createFromGlobals());
