<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Opis\Closure\SerializableClosure;

include_once __DIR__ . '/functions.php';

if (class_exists(SerializableClosure::class) && class_exists(\FFI::class)) {
    SerializableClosure::init();
}
