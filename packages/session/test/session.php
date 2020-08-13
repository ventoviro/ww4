<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Windwalker\Session\Bridge\PhpBridge;
use Windwalker\Session\Cookie\Cookies;
use Windwalker\Session\Handler\DatabaseHandler;
use Windwalker\Session\Handler\FilesystemHandler;
use Windwalker\Session\Handler\NativeHandler;
use Windwalker\Session\Session;

error_reporting(-1);

include_once __DIR__ . '/../../../vendor/autoload.php';

// $b = new NativeHandler();
// $b->read('');

$session = new Session(
    [
        'auto_commit' => false,
        'ini' => [
            'save_path' => dirname(__DIR__) . '/tmp/',
            'use_strict_mode' => '1',
            'use_cookies' => '0',
            'serialize_handler' => 'php_serialize',
        ]
    ],
    new \Windwalker\Session\Bridge\NativeBridge(
        [
            'gc_probability' => 1
        ],
        $n = new FilesystemHandler()
        // new DatabaseHandler(require __DIR__ . '/db-adapter.php')
    ),
    Cookies::create()
        ->httpOnly(true)
        ->expires('+30days')
        ->secure(false)
        ->sameSite(Cookies::SAMESITE_LAX)
);

$session->setName('WW_SESS');

$session->start();
show($_SESSION);
// show(session_save_path(), $n->write('qwe', 'asdawe'));
$session->addFlash('dsf', 'info');

$session->set('flower', 'Sakura');
die;
