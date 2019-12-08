<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Cache\Serializer;

/**
 * The PhpFileSerializer class.
 *
 * @since  3.0
 */
class PhpFileSerializer implements SerializerInterface
{
    /**
     * Encode data.
     *
     * @param   mixed $data
     *
     * @return  string
     */
    public function serialize($data)
    {
        return "<?php \n\nreturn " . var_export($data, true) . ';';
    }

    /**
     * Decode data.
     *
     * @param   string $data
     *
     * @return  mixed
     */
    public function unserialize($data)
    {
        return $data;
    }
}
