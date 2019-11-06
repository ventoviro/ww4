<?php declare(strict_types = 1);

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

namespace Windwalker {

    use Windwalker\Scalars\StringObject;

    /**
     * str
     *
     * @param string      $string
     * @param null|string $encoding
     *
     * @return  StringObject
     */
    function str($string = '', ?string $encoding = StringObject::ENCODING_UTF8): StringObject
    {
        return new StringObject((string) $string, $encoding);
    }
}
