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
use Windwalker\Session\Bridge\PhpBridge;
use Windwalker\Session\Cookies;
use Windwalker\Session\Handler\DatabaseHandler;

include_once __DIR__ . '/../../../vendor/autoload.php';

ini_set('session.use_strict_mode', '1');
// ini_set('session.gc_probability', '100');
// ini_set('session.gc_divisor', '100');

$cookie = Cookies::create()
    ->httpOnly(true)
    ->path('/')
    ->domain('localhost')
    ->sameSite(Cookies::SAMESITE_LAX)
    ->secure(false);

$db = new DatabaseAdapter(
    [
        'driver' => 'pdo_mysql',
        'database' => 'windwalker_test',
        'username' => 'root',
        'password' => '1234'
    ]
);

$db->on(QueryEndEvent::class, fn (QueryEndEvent $event) => show($event->getSql()));

$db->execute(file_get_contents(__DIR__ . '/../resources/sql/mysql.sql'));

$sess = new PhpBridge(
    new DatabaseHandler(
        $db
    )
);

if ($_COOKIE['WW_SESS_ID'] ?? null) {
    $sess->setId($_COOKIE['WW_SESS_ID']);
}

$sess->start();
$cookie->set('WW_SESS_ID', $sess->getId());

$_SESSION['flower'] = 'Sakura';

//
// $options = $cookie->getOptions();
//
// // ini_set('session.use_cookies', '0');
//
// // session_set_cookie_params($options);
//
// session_start();
//
// show(session_id());
//
// session_regenerate_id();
//
// show(session_id());
