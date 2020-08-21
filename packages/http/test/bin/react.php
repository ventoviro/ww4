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

use Windwalker\Http\Event\WebRequestEvent;
use Windwalker\Http\Server\Adapter\ReactServerAdapter;
use Windwalker\Http\Server\HttpServer;

$server = new HttpServer(new ReactServerAdapter('0.0.0.0', 8888));
$server->on(
    'request',
    static function (WebRequestEvent $event) {
        $app = require __DIR__ . '/app.php';

        $res = $app($event->getRequest());

        $event->setResponse($res);
    }
);
$server->on(
    'error',
    static function ($event) {
        echo $event->getException();
    }
);
$server->listen();
