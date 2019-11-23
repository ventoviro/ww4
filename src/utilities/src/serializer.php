<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker;

use Opis\Closure\SerializableClosure;

function serialize($data): string {
    if (!class_exists(SerializableClosure::class)) {
        throw new \LogicException('Please install opis/closure first');
    }

    return \Opis\Closure\serialize($data);
}

function unserialize(string $data) {
    if (!class_exists(SerializableClosure::class)) {
        throw new \LogicException('Please install opis/closure first');
    }

    return \Opis\Closure\unserialize($data);
}

function closure(\Closure $closure) {
    if (!class_exists(SerializableClosure::class)) {
        throw new \LogicException('Please install opis/closure first');
    }

    return new SerializableClosure($closure);
}
