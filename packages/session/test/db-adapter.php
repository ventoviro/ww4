<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Windwalker\Database\DatabaseAdapter;
use Windwalker\Database\Event\QueryEndEvent;

$db = new DatabaseAdapter(
    [
        'host' => '127.0.0.1',
        'driver' => 'pdo_mysql',
        'database' => 'windwalker_test',
        'username' => 'root',
        'password' => '1234'
    ]
);

$db->on(QueryEndEvent::class, fn (QueryEndEvent $event) => show($event->getSql()));

$db->execute(file_get_contents(__DIR__ . '/../resources/sql/mysql.sql'));

return $db;
