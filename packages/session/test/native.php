<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

error_reporting(-1);

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Event\QueryEndEvent;
use Windwalker\Session\Bridge\NativeBridge;
use Windwalker\Session\Cookie\Cookies;
use Windwalker\Session\Handler\DatabaseHandler;
use Windwalker\Session\Handler\NativeHandler;

include_once __DIR__ . '/../../../vendor/autoload.php';

session_save_path(dirname(__DIR__) . '/tmp/');
session_set_save_handler(
    new DatabaseHandler(require_once __DIR__ . '/db-adapter.php')
);
session_set_save_handler(new NativeHandler());

session_start();

show($_SESSION);

$_SESSION['flower'] = 'Sakura';

session_write_close();
