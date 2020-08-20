<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later;
 */

declare(strict_types=1);

namespace Windwalker\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Windwalker\Http\Request\Request;
use Windwalker\Http\Transport\CurlTransport;
use Windwalker\Http\Transport\TransportInterface;
use Windwalker\Http\Uri\Uri;
use Windwalker\Http\Uri\UriHelper;

/**
 * The HttpClient class.
 *
 * @since  2.1
 */
class HttpClient implements HttpClientInterface
{
    public const MULTIPART_FORMDATA = 'multipart/form-data';

    /**
     * Property options.
     *
     * @var  array
     */
    protected $options = [];

    /**
     * Property transport.
     *
     * @var  TransportInterface
     */
    protected $transport;

    /**
     * create
     *
     * @param array                   $options
     * @param TransportInterface|null $transport
     *
     * @return  static
     *
     * @since  3.5.19
     */
    public static function create(array $options = [], TransportInterface $transport = null)
    {
        return new static($options, $transport);
    }

    /**
     * Class init.
     *
     * @param  array              $options   The options of this client object.
     * @param  TransportInterface $transport The Transport handler, default is CurlTransport.
     */
    public function __construct($options = [], TransportInterface $transport = null)
    {
        $this->options = (array) $options;
        $this->transport = $transport ?: new CurlTransport();
    }

    /**
     * Request a remote server.
     *
     * This method will build a Request object and use send() method to send request.
     *
     * @param string        $method  The method type.
     * @param string|object $url     The URL to request, may be string or Uri object.
     * @param mixed         $data    The request body data, can be an array of POST data.
     * @param array         $headers The headers array.
     *
     * @return  ResponseInterface
     */
    public function request($method, $url, $data = null, $headers = [])
    {
        $request = $this->preprocessRequest(new Request(), $method, $url, $data, $headers);

        return $this->sendRequest($request);
    }

    /**
     * Download file to target path.
     *
     * @param string|object $url     The URL to request, may be string or Uri object.
     * @param string|       $dest    The dest file path can be a StreamInterface.
     * @param mixed         $data    The request body data, can be an array of POST data.
     * @param array         $headers The headers array.
     *
     * @return  ResponseInterface
     */
    public function download($url, $dest, $data = null, $headers = [])
    {
        $request = $this->preprocessRequest(new Request(), 'GET', $url, $data, $headers);

        $transport = $this->getTransport();

        if (!$transport::isSupported()) {
            throw new \RangeException(get_class($transport) . ' driver not supported.');
        }

        return $transport->download($request, $dest);
    }

    /**
     * Send a request to remote.
     *
     * @param   RequestInterface $request The Psr Request object.
     *
     * @return  ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $transport = $this->getTransport();

        if (!$transport::isSupported()) {
            throw new \RangeException(get_class($transport) . ' driver not supported.');
        }

        return $transport->request($request);
    }

    /**
     * Method to send the OPTIONS command to the server.
     *
     * @param   string $url     Path to the resource.
     * @param   array  $headers An array of name-value pairs to include in the header of the request.
     *
     * @return  ResponseInterface
     *
     * @since   2.1
     */
    public function options($url, $headers = [])
    {
        return $this->request('OPTIONS', $url, null, $headers);
    }

    /**
     * Method to send the HEAD command to the server.
     *
     * @param   string $url     Path to the resource.
     * @param   array  $headers An array of name-value pairs to include in the header of the request.
     *
     * @return  ResponseInterface
     *
     * @since   2.1
     */
    public function head($url, $headers = [])
    {
        return $this->request('HEAD', $url, null, $headers);
    }

    /**
     * Method to send the GET command to the server.
     *
     * @param   string $url     Path to the resource.
     * @param   mixed  $data    Either an associative array or a string to be sent with the request.
     * @param   array  $headers An array of name-value pairs to include in the header of the request.
     *
     * @return  ResponseInterface
     *
     * @since   2.1
     */
    public function get($url, $data = null, $headers = [])
    {
        return $this->request('GET', $url, $data, $headers);
    }

    /**
     * Method to send the POST command to the server.
     *
     * @param   string $url     Path to the resource.
     * @param   mixed  $data    Either an associative array or a string to be sent with the request.
     * @param   array  $headers An array of name-value pairs to include in the header of the request
     *
     * @return  ResponseInterface
     *
     * @since   2.1
     */
    public function post($url, $data, $headers = [])
    {
        return $this->request('POST', $url, $data, $headers);
    }

    /**
     * Method to send the PUT command to the server.
     *
     * @param   string $url     Path to the resource.
     * @param   mixed  $data    Either an associative array or a string to be sent with the request.
     * @param   array  $headers An array of name-value pairs to include in the header of the request.
     *
     * @return  ResponseInterface
     *
     * @since   2.1
     */
    public function put($url, $data, $headers = [])
    {
        return $this->request('PUT', $url, $data, $headers);
    }

    /**
     * Method to send the DELETE command to the server.
     *
     * @param   string $url     Path to the resource.
     * @param   mixed  $data    Either an associative array or a string to be sent with the request.
     * @param   array  $headers An array of name-value pairs to include in the header of the request.
     *
     * @return  ResponseInterface
     *
     * @since   2.1
     */
    public function delete($url, $data = null, $headers = [])
    {
        return $this->request('DELETE', $url, $data, $headers);
    }

    /**
     * Method to send the TRACE command to the server.
     *
     * @param   string $url     Path to the resource.
     * @param   array  $headers An array of name-value pairs to include in the header of the request.
     *
     * @return  ResponseInterface
     *
     * @since   2.1
     */
    public function trace($url, $headers = [])
    {
        return $this->request('TRACE', $url, null, $headers);
    }

    /**
     * Method to send the PATCH command to the server.
     *
     * @param   string $url     Path to the resource.
     * @param   mixed  $data    Either an associative array or a string to be sent with the request.
     * @param   array  $headers An array of name-value pairs to include in the header of the request.
     *
     * @return  ResponseInterface
     *
     * @since   2.1
     */
    public function patch($url, $data, $headers = [])
    {
        return $this->request('PATCH', $url, $data, $headers);
    }

    /**
     * Get option value.
     *
     * @param   string $name    Option name.
     * @param   mixed  $default The default value if not exists.
     *
     * @return  mixed  The found value or default value.
     */
    public function getOption($name, $default = null)
    {
        if (!isset($this->options[$name])) {
            return $default;
        }

        return $this->options[$name];
    }

    /**
     * Set option value.
     *
     * @param   string $name  Option name.
     * @param   mixed  $value The value you want to set in.
     *
     * @return  static  Return self to support chaining.
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Method to get property Options
     *
     * @return  array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Method to set property options
     *
     * @param   array $options
     *
     * @return  static  Return self to support chaining.
     */
    public function setOptions($options)
    {
        if ($options instanceof \Traversable) {
            $options = iterator_to_array($options);
        }

        if (is_object($options)) {
            $options = get_object_vars($options);
        }

        $this->options = (array) $options;

        return $this;
    }

    /**
     * Method to get property Transport
     *
     * @return  TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * Method to set property transport
     *
     * @param   TransportInterface $transport
     *
     * @return  static  Return self to support chaining.
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;

        return $this;
    }

    /**
     * Create Request object.
     *
     * @param   string           $method  The method type.
     * @param   string|object    $url     The URL to request, may be string or Uri object.
     * @param   mixed            $data    The request body data, can be an array of POST data.
     * @param   array            $headers The headers array.
     *
     * @return  RequestInterface
     *
     * @since  3.5.6
     */
    public static function createRequest(string $method, $url, $data = '', array $headers = []): RequestInterface
    {
        return static::prepareRequest(new Request(), $method, $url, $data, $headers);
    }

    /**
     * Prepare Request object.
     *
     * @param   RequestInterface $request The Psr Request object.
     * @param   string           $method  The method type.
     * @param   string|object    $url     The URL to request, may be string or Uri object.
     * @param   mixed            $data    The request body data, can be an array of POST data.
     * @param   array            $headers The headers array.
     *
     * @return  RequestInterface
     *
     * @since  3.5.6
     */
    public static function prepareRequest(
        RequestInterface $request,
        string $method,
        $url,
        $data = '',
        array $headers = []
    ): RequestInterface {
        // If is GET, we merge data into URL.
        if (is_array($data) && strtoupper($method) === 'GET') {
            $url = Uri::wrap($url);

            foreach ($data as $k => $v) {
                $url = $url->withVar($k, $v);
            }

            $url = (string) $url;
            $data = null;
        }

        $url = (string) $url;

        $request = $request->withRequestTarget((string) $url)
            ->withMethod($method);

        // Override with this method
        foreach ($headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        // If not GET, convert data to query string.
        if (is_array($data)) {
            if (str_starts_with($request->getHeaderLine('Content-Type'), 'multipart/form-data')) {
                $data = serialize($data);
            } else {
                $data = UriHelper::buildQuery($data);
            }
        }

        /** @var RequestInterface $request */
        $request->getBody()->write((string) $data);

        return $request;
    }

    /**
     * Prepare Request object to send request.
     *
     * @param   RequestInterface $request The Psr Request object.
     * @param   string           $method  The method type.
     * @param   string|object    $url     The URL to request, may be string or Uri object.
     * @param   mixed            $data    The request body data, can be an array of POST data.
     * @param   array            $headers The headers array.
     *
     * @return  RequestInterface
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    protected function preprocessRequest(RequestInterface $request, $method, $url, $data, $headers)
    {
        // Set global headers
        foreach ((array) $this->getOption('headers') as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        return static::prepareRequest($request, $method, $url, $data, $headers);
    }
}
