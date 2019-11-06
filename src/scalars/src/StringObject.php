<?php declare(strict_types = 1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT.
 * @license    Please see LICENSE file.
 */

namespace Windwalker\Scalars;

use Traversable;
use Windwalker\Scalars\Concern\StringModifyTrait;
use Windwalker\Scalars\Concern\StringPositionTrait;
use Windwalker\Utilities\Assert\ArgumentsAssert;
use Windwalker\Utilities\Classes\ImmutableHelperTrait;
use Windwalker\Utilities\Classes\StringableInterface;
use Windwalker\Utilities\Str;
use Windwalker\Utilities\Utf8String;
use function Windwalker\tap;

/**
 * The StringObject class.
 *
 * @see  Str
 *
 * @method StringObject getChar(int $pos)
 * @method StringObject between(string $start, string $end, int $offset = 0)
 * @method StringObject collapseWhitespaces(string $string)
 * @method bool         contains(string $search, bool $caseSensitive = true)
 * @method bool         endsWith(string $search, bool $caseSensitive = true)
 * @method bool         startsWith(string $target, bool $caseSensitive = true)
 * @method StringObject ensureLeft(string $search)
 * @method StringObject ensureRight(string $search)
 * @method bool         hasLowerCase()
 * @method bool         hasUpperCase()
 * @method StringObject match(string $pattern, string $option = 'msr')
 * @method StringObject insert(string $insert, int $position)
 * @method bool         isLowerCase()
 * @method bool         isUpperCase()
 * @method StringObject first(int $length = 1)
 * @method StringObject last(int $length = 1)
 * @method StringObject intersectLeft(string $string2)
 * @method StringObject intersectRight(string $string2)
 * @method StringObject intersect(string $string2)
 * @method StringObject pad(int $length = 0, string $substring = ' ')
 * @method StringObject padLeft(int $length = 0, string $substring = ' ')
 * @method StringObject padRight(int $length = 0, string $substring = ' ')
 * @method StringObject removeChar(int $offset, int $length = null)
 * @method StringObject removeLeft(string $search)
 * @method StringObject removeRight(string $search)
 * @method StringObject slice(int $start, int $end = null)
 * @method StringObject substring(int $start, int $end = null)
 * @method StringObject wrap($substring = ['"', '"'])
 * @method StringObject toggleCase()
 * @method StringObject truncate(int $length, string $suffix = '', bool $wordBreak = true)
 * @method StringObject map(callable $callback)
 * @method StringObject filter(callable $callback)
 * @method StringObject reject(callable $callback)
 * @method StringObject toUpperCase()
 * @method StringObject toLowerCase()
 * @method int|bool     strpos(string $search)
 * @method int|bool     strrpos(string $search)
 * @method StringObject split(string $delimiter, ?int $limit = null)
 *
 * @since  __DEPLOY_VERSION__
 */
class StringObject implements \Countable, \ArrayAccess, \IteratorAggregate, StringableInterface, ScalarsInterface
{
    use ImmutableHelperTrait;
    use StringModifyTrait;
    use StringPositionTrait;

    /**
     * We only provides 3 default encoding constants of PHP.
     * @see http://php.net/manual/en/xml.encoding.php
     */
    public const ENCODING_DEFAULT_ISO = 'ISO-8859-1';
    public const ENCODING_UTF8 = 'UTF-8';
    public const ENCODING_US_ASCII = 'US-ASCII';

    /**
     * Property string.
     *
     * @var  string
     */
    protected string $string = '';

    /**
     * Property encoding.
     *
     * @var  string
     */
    protected ?string $encoding = null;

    /**
     * create
     *
     * @param string      $string
     * @param null|string $encoding
     *
     * @return  static
     */
    public static function create(string $string = '', ?string $encoding = self::ENCODING_UTF8): StringObject
    {
        return new static($string, $encoding);
    }

    /**
     * StringObject constructor.
     *
     * @see  http://php.net/manual/en/mbstring.supported-encodings.php
     *
     * @param string      $string
     * @param null|string $encoding
     */
    public function __construct(string $string = '', ?string $encoding = self::ENCODING_UTF8)
    {
        $this->string   = $string;
        $this->encoding = $encoding ?? static::ENCODING_UTF8;
    }

    /**
     * __call
     *
     * @param  string  $name
     * @param  array   $args
     *
     * @return  mixed
     * @throws \BadMethodCallException
     * @throws \ReflectionException
     */
    public function __call(string $name, array $args)
    {
        $class = Str::class;

        if (is_callable([$class, $name])) {
            return $this->callProxy($class, $name, $args);
        }

        $maps = [
            'toUpperCase' => [Utf8String::class, 'strtoupper'],
            'toLowerCase' => [Utf8String::class, 'strtolower'],
            'split' => [$this, 'explode'],
        ];

        if ($maps[$name] ?? null) {
            return $this->callProxy($maps[$name][0], $maps[$name][1], $args);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method: %s::%s()', static::class, $name));
    }

    /**
     * callProxy
     *
     * @param  string  $class
     * @param  string  $method
     * @param  array   $args
     *
     * @return  static
     * @throws \ReflectionException
     */
    protected function callProxy(string $class, string $method, array $args)
    {
        $new = $this->cloneInstance();

        $closure = \Closure::fromCallable([$class, $method]);

        if (method_exists($class, $method)) {
            $ref = new \ReflectionMethod($class, $method);
        } else {
            $ref = (new \ReflectionObject($closure))->getMethod('__invoke');
        }

        $params = $ref->getParameters();

        array_shift($params);

        /** @var \ReflectionParameter $param */
        foreach (array_values($params) as $k => $param) {
            if (!array_key_exists($k, $args)) {
                if ($param->getName() === 'encoding' && !isset($args[$k])) {
                    $args[$k] = $this->encoding;
                    continue;
                }

                $args[$k] = $param->getDefaultValue();
            }
        }

        $result = $closure($new->string, ...$args);

        if (is_string($result)) {
            $new->string = $result;

            return $new;
        }

        return $result;
    }

    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->chop());
    }

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        $offset = $offset >= 0 ? $offset : (int) abs($offset) - 1;

        return $this->length() > $offset;
    }

    /**
     * Offset to retrieve
     *
     * @param int $offset The offset to retrieve.
     *
     * @return string Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->getChar($offset);
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $string <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $string)
    {
        $this->string = Utf8String::substrReplace($this->string, $string, $offset, 1, $this->encoding);
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if ($this->length() < abs($offset)) {
            return;
        }

        $this->string = Str::removeChar($this->string, $offset, 1, $this->encoding);
    }

    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *        </p>
     *        <p>
     *        The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return $this->length();
    }

    /**
     * Magic method to convert this object to string.
     *
     * @return  string
     */
    public function __toString(): string
    {
        return (string) $this->string;
    }

    /**
     * Method to get property Encoding
     *
     * @return  string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Method to set property encoding
     *
     * @param   string $encoding
     *
     * @return  static  Return self to support chaining.
     */
    public function withEncoding(string $encoding): self
    {
        return $this->cloneInstance(function (StringObject $new) use ($encoding) {
            $new->encoding = $encoding;
        });
    }

    /**
     * Method to get property String
     *
     * @return  string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * Method to set property string
     *
     * @param   string $string
     *
     * @return  static  Return self to support chaining.
     */
    public function withString(string $string): self
    {
        return $this->cloneInstance(function (StringObject $new) use ($string) {
            $new->string = $string;
        });
    }

    /**
     * length
     *
     * @return  int
     */
    public function length(): int
    {
        return Utf8String::strlen($this->string, $this->encoding);
    }

    /**
     * split
     *
     * @param int $length
     *
     * @return  ArrayObject
     */
    public function chop($length = 1)
    {
        ArgumentsAssert::assert($length >= 1, '%s $length must larger than 1, %s given', $length);

        return new ArrayObject(Utf8String::strSplit($this->string, $length, $this->encoding) ?: []);
    }

    /**
     * replace
     *
     * @param array|string $search
     * @param array|string $replacement
     * @param int|null     $count
     *
     * @return  static
     */
    public function replace($search, $replacement, int &$count = null): self
    {
        return $this->cloneInstance(function (StringObject $new) use ($search, $replacement, &$count) {
            $new->string = str_replace($search, $replacement, $new->string, $count);
        });
    }

    /**
     * compare
     *
     * @param string $compare
     * @param bool   $caseSensitive
     *
     * @return  int
     */
    public function compare(string $compare, bool $caseSensitive = true): int
    {
        if ($caseSensitive) {
            return Utf8String::strcmp($this->string, $compare);
        }

        return Utf8String::strcasecmp($this->string, $compare, $this->encoding);
    }

    /**
     * reverse
     *
     * @return  static
     */
    public function reverse(): self
    {
        return $this->cloneInstance(function (StringObject $new) {
            $new->string = Utf8String::strrev($new->string);
        });
    }

    /**
     * substrReplace
     *
     * @param string $replace
     * @param int    $start
     * @param int    $offset
     *
     * @return  static
     */
    public function substrReplace(string $replace, int $start, int $offset = null): self
    {
        return $this->cloneInstance(function (StringObject $new) use ($replace, $start, $offset) {
            $new->string = Utf8String::substrReplace($new->string, $replace, $start, $offset, $this->encoding);
        });
    }

    /**
     * ltrim
     *
     * @param string|null $charlist
     *
     * @return  static
     */
    public function trimLeft(string $charlist = null): self
    {
        return $this->cloneInstance(function (StringObject $new) use ($charlist) {
            $new->string = Utf8String::ltrim($new->string, $charlist);
        });
    }

    /**
     * rtrim
     *
     * @param string|null $charlist
     *
     * @return  static
     */
    public function trimRight(string $charlist = null): self
    {
        return $this->cloneInstance(function (StringObject $new) use ($charlist) {
            $new->string = Utf8String::rtrim($new->string, $charlist);
        });
    }

    /**
     * trim
     *
     * @param string|null $charlist
     *
     * @return  static
     */
    public function trim(string $charlist = null): self
    {
        return $this->cloneInstance(function (StringObject $new) use ($charlist) {
            $new->string = Utf8String::trim($new->string, $charlist);
        });
    }

    /**
     * ucfirst
     *
     * @return  static
     */
    public function upperCaseFirst(): self
    {
        return $this->cloneInstance(function (StringObject $new) {
            $new->string = Utf8String::ucfirst($new->string, $this->encoding);
        });
    }

    /**
     * lcfirst
     *
     * @return  static
     */
    public function lowerCaseFirst(): self
    {
        return $this->cloneInstance(function (StringObject $new) {
            $new->string = Utf8String::lcfirst($new->string, $this->encoding);
        });
    }

    /**
     * upperCaseWords
     *
     * @return  static
     */
    public function upperCaseWords(): self
    {
        return $this->cloneInstance(function (StringObject $new) {
            $new->string = Utf8String::ucwords($new->string, $this->encoding);
        });
    }

    /**
     * substrCount
     *
     * @param string $search
     * @param bool   $caseSensitive
     *
     * @return  int
     */
    public function substrCount(string $search, bool $caseSensitive = true): int
    {
        return Utf8String::substrCount($this->string, $search, $caseSensitive, $this->encoding);
    }

    /**
     * indexOf
     *
     * @param string $search
     *
     * @return  int
     */
    public function indexOf(string $search): int
    {
        $result = Utf8String::strpos($this->string, $search, 0, $this->encoding);

        if ($result === false) {
            return -1;
        }

        return $result;
    }

    /**
     * indexOf
     *
     * @param string $search
     *
     * @return  int
     */
    public function indexOfLast(string $search): int
    {
        $result = Utf8String::strrpos($this->string, $search, 0, $this->encoding);


        if ($result === false) {
            return -1;
        }

        return $result;
    }

    /**
     * explode
     *
     * @param string   $delimiter
     * @param int|null $limit
     *
     * @return  ArrayObject
     */
    public function explode(string $delimiter, ?int $limit = null): ArrayObject
    {
        $limit ??= PHP_INT_MAX;

        return ArrayObject::explode($delimiter, $this->string, $limit);
    }

    /**
     * apply
     *
     * @param callable $callback
     *
     * @return  static
     */
    public function apply(callable $callback): self
    {
        return $this->cloneInstance(static function ($new) use ($callback) {
            $new->string = $callback($new->string);
        });
    }

    /**
     * pipe
     *
     * @param  callable  $callback
     *
     * @return  static
     *
     * @since  3.5.14
     */
    public function pipe(callable $callback): self
    {
        return $callback($this);
    }

    /**
     * clearHtml
     *
     * @param string|null $allowTags
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function stripHtmlTags(?string $allowTags = null): self
    {
        return $this->cloneInstance(static function (self $new) use ($allowTags) {
            $new->string = strip_tags($new->string, $allowTags);
        });
    }

    /**
     * append
     *
     * @param string|StringObject $string
     *
     * @return  StringObject
     *
     * @since  __DEPLOY_VERSION__
     */
    public function append($string): self
    {
        return tap(clone $this, static function (StringObject $new) use ($string) {
            $new->string .= $string;
        });
    }

    /**
     * prepend
     *
     * @param string|StringObject $string
     *
     * @return  StringObject
     *
     * @since  __DEPLOY_VERSION__
     */
    public function prepend($string): self
    {
        return tap(clone $this, static function (StringObject $new) use ($string) {
            $new->string = $string . $new->string;
        });
    }

    public function toString(): self
    {
        return clone $this;
    }

    public function toArray(): ArrayObject
    {
        return new ArrayObject([$this]);
    }
}
