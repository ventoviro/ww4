<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later.
 */

declare(strict_types=1);

namespace Windwalker\Utilities;

/**
 * The SimpleTemplate class.
 *
 * @since  2.1.8
 */
class SimpleTemplate
{
    /**
     * @var  array
     */
    protected $wrapper = ['{{', '}}'];

    /**
     * @var  string
     */
    protected $delimiter = '.';

    public static function create(): SimpleTemplate
    {
        return new static();
    }

    public function renderTemplate(string $string, array $data = []): string
    {
        [$begin, $end] = $this->wrapper;

        $regex = preg_quote($begin) . '\s*(.+?)\s*' . preg_quote($end);

        return preg_replace_callback(
            chr(1) . $regex . chr(1),
            function ($match) use ($data) {
                $return = Arr::get($data, $match[1], $this->delimiter);

                if (is_array($return) || is_object($return)) {
                    return TypeCast::toString($return);
                }

                return $return;
            },
            $string
        );
    }

    /**
     * Parse variable and replace it. This method is a simple template engine.
     *
     * Example: The {{ foo.bar.yoo }} will be replace to value of `$data['foo']['bar']['yoo']`
     *
     * @param  string  $string  The template to replace.
     * @param  array   $data    The data to find.
     *
     * @return  string Replaced template.
     */
    public static function render(string $string, array $data = []): string
    {
        return (new static())->renderTemplate($string, $data);
    }

    public function setVarWrapper(string $start, string $end): SimpleTemplate
    {
        $this->wrapper = [$start, $end];

        return $this;
    }

    /**
     * Method to get property Delimiter
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * Method to set property delimiter
     *
     * @param  string  $delimiter
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }
}
