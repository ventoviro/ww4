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
 * Interface DataHandlerInterface
 */
interface SerializerInterface
{
    /**
     * Encode data.
     *
     * @param  mixed  $data
     *
     * @return  mixed
     */
    public function serialize($data);

    /**
     * Decode data.
     *
     * @param  mixed  $data
     *
     * @return  mixed
     */
    public function unserialize($data);
}
