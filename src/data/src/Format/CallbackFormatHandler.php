<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Data\Format;

/**
 * The CallbackFormatHandler class.
 *
 * @since  __DEPLOY_VERSION__
 */
class CallbackFormatHandler implements FormatInterface
{
    /**
     * @var  callable|null
     */
    protected $parser;

    /**
     * @var  callable|null
     */
    protected $dumper;

    /**
     * CallbackFormatHandler constructor.
     *
     * @param  callable  $parser
     * @param  callable  $dumper
     */
    public function __construct(?callable $parser = null, ?callable $dumper = null)
    {
        $this->parser = $parser;
        $this->dumper = $dumper;
    }

    /**
     * Converts an object into a formatted string.
     *
     * @param  array|object  $data     Data Source Object.
     * @param  array         $options  An array of options for the formatter.
     *
     * @return  string  Formatted string.
     *
     * @since   2.0
     */
    public function dump($data, array $options = []): string
    {
        return ($this->dumper)($data, $options);
    }

    /**
     * Converts a formatted string into an object.
     *
     * @param  string  $string   Formatted string
     * @param  array   $options  An array of options for the formatter.
     *
     * @return array Data array
     *
     * @since   2.0
     */
    public function parse(string $string, array $options = []): array
    {
        return ($this->parser)($string, $options);
    }

    /**
     * Method to get property Parser
     *
     * @return  callable
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getParser(): callable
    {
        return $this->parser;
    }

    /**
     * Method to set property parser
     *
     * @param  callable  $parser
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setParser(?callable $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Method to get property Dumper
     *
     * @return  callable
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getDumper(): callable
    {
        return $this->dumper;
    }

    /**
     * Method to set property dumper
     *
     * @param  callable  $dumper
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setDumper(?callable $dumper)
    {
        $this->dumper = $dumper;

        return $this;
    }
}
