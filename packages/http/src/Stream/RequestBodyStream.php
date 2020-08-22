<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Http\Stream;

use Psr\Http\Message\StreamInterface;
use Windwalker\Http\Uri\UriHelper;

/**
 * The RequestBodyStream class.
 */
class RequestBodyStream implements StreamInterface
{
    protected array $data = [];

    protected int $cursor = 0;

    /**
     * RequestBodyStream constructor.
     *
     * @param  array  $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * __toString
     *
     * @return  string
     */
    public function __toString()
    {
        return UriHelper::buildQuery($this->data);
    }

    /**
     * close
     *
     * @return  void
     */
    public function close(): void
    {
        //
    }

    /**
     * detach
     *
     * @return  resource|null
     */
    public function detach()
    {
        $this->data = [];

        return null;
    }

    /**
     * getSize
     *
     * @return  int|null
     */
    public function getSize(): ?int
    {
        return strlen((string) $this);
    }

    /**
     * tell
     *
     * @return  int
     */
    public function tell()
    {
        return $this->cursor;
    }

    /**
     * eof
     *
     * @return  bool
     */
    public function eof(): bool
    {
        return $this->cursor === (count($this->data) - 1);
    }

    /**
     * isSeekable
     *
     * @return  bool
     */
    public function isSeekable(): bool
    {
        return true;
    }

    /**
     * seek
     *
     * @param  int  $offset
     * @param  int  $whence
     *
     * @return  mixed
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->cursor = $offset;

        return true;
    }

    /**
     * rewind
     *
     * @return  mixed
     */
    public function rewind()
    {
        $this->cursor = 0;

        return true;
    }

    /**
     * isWritable
     *
     * @return  bool
     */
    public function isWritable()
    {
        return true;
    }

    /**
     * write
     *
     * @param  string  $string
     *
     * @return  int
     */
    public function write($string)
    {
        if (is_string($string)) {
            $this->data = UriHelper::parseQuery($string);
        } else {
            $this->data = (array) $string;
        }

        return $this->getSize();
    }

    /**
     * isReadable
     *
     * @return  bool
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * read
     *
     * @param  int  $length
     *
     * @return  string
     */
    public function read($length)
    {
        return UriHelper::buildQuery($this->data);
    }

    /**
     * getContents
     *
     * @return  string
     */
    public function getContents()
    {
        return UriHelper::buildQuery($this->data);
    }

    /**
     * getMetadata
     *
     * @param  string|null  $key
     *
     * @return  array|mixed|null
     */
    public function getMetadata($key = null)
    {

    }
}
