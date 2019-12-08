<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

include_once __DIR__ . '/functions.php';

// WeakReference polyfill
if (!class_exists(WeakReference::class)) {
    include_once __DIR__ . '/weak_reference.php';
}
