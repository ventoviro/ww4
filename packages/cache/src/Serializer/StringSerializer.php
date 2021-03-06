<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\Cache\Serializer;

/**
 * Class RawHandler
 *
 * @since 2.0
 */
class StringSerializer implements SerializerInterface
{
    /**
     * Encode data.
     *
     * @param  mixed  $data
     *
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function serialize($data): ?string
    {
        if (!is_stringable($data)) {
            throw new \InvalidArgumentException(__CLASS__ . ' can not handle an array or non-stringable object.');
        }

        return (string) $data;
    }

    /**
     * Decode data.
     *
     * @param  string  $data
     *
     * @return string|null
     */
    public function unserialize(string $data)
    {
        return $data;
    }
}
