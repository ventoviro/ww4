<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

error_reporting(-1);

use Windwalker\Session\CookieSetter;

include_once __DIR__ . '/../../../vendor/autoload.php';

$cookie = CookieSetter::create()
    ->httpOnly(true)
    ->path('/')
    ->domain('localhost')
    ->sameSite(CookieSetter::SAMESITE_LAX)
    ->secure(false);

$options = $cookie->getOptions();

// ini_set('session.use_cookies', '0');

// session_set_cookie_params($options);

session_start();

show(session_id());

session_regenerate_id();

show(session_id());
