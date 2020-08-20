<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Http\Test\Transport;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;
use Psr\Http\Message\StreamInterface;
use Windwalker\Http\Request\Request;
use Windwalker\Http\Transport\AbstractTransport;
use Windwalker\Http\Uri\Uri;
use Windwalker\Http\Uri\UriHelper;
use Windwalker\Stream\Stream;
use Windwalker\Stream\StringStream;
use Windwalker\Test\Traits\BaseAssertionTrait;

/**
 * Test class of CurlTransport
 *
 * @since 2.1
 */
abstract class AbstractTransportTest extends \PHPUnit\Framework\TestCase
{
    use BaseAssertionTrait;

    /**
     * Property options.
     *
     * @var  array
     */
    protected array $options = [
        'options' => [],
    ];

    /**
     * Test instance.
     *
     * @var AbstractTransport
     */
    protected $instance;

    /**
     * setUpBeforeClass
     *
     * @return  void
     */
    public static function setUpBeforeClass(): void
    {
        if (!defined('WINDWALKER_TEST_HTTP_URL')) {
            static::markTestSkipped('No WINDWALKER_TEST_HTTP_URL provided');
        }
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        if (!$this->instance->isSupported()) {
            $this->markTestSkipped(get_class($this->instance) . ' driver not supported.');
        }
    }

    /**
     * createRequest
     *
     * @param StreamInterface $stream
     *
     * @return Request
     */
    protected function createRequest($stream = null)
    {
        return new Request($stream ?: new StringStream());
    }

    /**
     * testRequestGet
     *
     * @return  void
     */
    public function testRequestGet()
    {
        $request = $this->createRequest();

        $request = $request->withUri(new Uri(WINDWALKER_TEST_HTTP_URL . '/json?foo=bar'))
            ->withMethod('GET');

        $response = $this->instance->request($request);

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($response->getBody()->getContents());

        $request = $this->createRequest();

        $request = $request->withUri(new Uri(WINDWALKER_TEST_HTTP_URL . '/json?foo=bar&baz[3]=yoo'))
            ->withMethod('GET');

        $response = $this->instance->request($request);

        $data = json_decode($response->getBody()->getContents(), true);
        self::assertEquals(['foo' => 'bar', 'baz' => [3 => 'yoo']], $data);
    }

    /**
     * testBadDomainGet
     *
     * @return  void
     */
    public function testBadDomainGet()
    {
        $this->expectException(\RuntimeException::class);

        $request = $this->createRequest();

        $request = $request->withUri(new Uri('http://not.exists.url/flower.sakura'))
            ->withMethod('GET');

        $this->instance->request($request);
    }

    /**
     * testBadPathGet
     *
     * @return  void
     */
    public function testBadPathGet()
    {
        $request = $this->createRequest();

        $request = $request->withUri(new Uri(WINDWALKER_TEST_HTTP_URL . '/wrong'))
            ->withMethod('POST');

        $request->getBody()->write(UriHelper::buildQuery(['foo' => 'bar']));

        $response = $this->instance->request($request);

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals('Not Found', $response->getReasonPhrase());
    }

    /**
     * testRequestPost
     *
     * @return  void
     */
    public function testRequestPost()
    {
        $request = $this->createRequest();

        $request = $request->withUri(new Uri(WINDWALKER_TEST_HTTP_URL))
            ->withMethod('POST');

        $request->getBody()->write(UriHelper::buildQuery(['foo' => 'bar']));

        $response = $this->instance->request($request);

        self::assertStringSafeEquals(
            <<<BODY
            POST http://localhost:8163/
            host: localhost:8163
            accept: */*
            content-type: application/x-www-form-urlencoded; charset=utf-8
            content-length: 7

            foo=bar
            BODY,
            $response->getBody()->getContents()
        );
    }

    /**
     * testRequestPut
     *
     * @return  void
     */
    public function testRequestPut()
    {
        $request = $this->createRequest();

        $request = $request->withUri(new Uri(WINDWALKER_TEST_HTTP_URL))
            ->withMethod('PUT');

        $request->getBody()->write(UriHelper::buildQuery(['foo' => 'bar']));

        $response = $this->instance->request($request);

        self::assertStringSafeEquals(
            <<<BODY
            PUT http://localhost:8163/
            host: localhost:8163
            accept: */*
            content-type: application/x-www-form-urlencoded; charset=utf-8
            content-length: 7

            foo=bar
            BODY,
            $response->getBody()->getContents()
        );
    }

    /**
     * testRequestCredentials
     *
     * @return  void
     */
    public function testRequestCredentials()
    {
        $request = $this->createRequest();

        $uri = new Uri(WINDWALKER_TEST_HTTP_URL);
        $uri = $uri->withUserInfo('username', 'pass1234');

        $request = $request->withUri($uri)
            ->withMethod('GET');

        $response = $this->instance->request($request);

        self::assertStringSafeEquals(
            <<<BODY
            GET http://localhost:8163/
            host: localhost:8163
            authorization: Basic dXNlcm5hbWU6cGFzczEyMzQ=
            accept: */*
            content-type: application/x-www-form-urlencoded; charset=utf-8
            BODY,
            $response->getBody()->getContents()
        );
    }

    /**
     * testRequestPostScalar
     *
     * @return  void
     */
    public function testRequestPostScalar()
    {
        $request = $this->createRequest();

        $request = $request->withUri(new Uri(WINDWALKER_TEST_HTTP_URL . '?foo=bar'))
            ->withMethod('POST');

        $request->getBody()->write('flower=sakura');

        $response = $this->instance->request($request);

        self::assertStringSafeEquals(
            <<<BODY
            POST http://localhost:8163/?foo=bar
            host: localhost:8163
            accept: */*
            content-type: application/x-www-form-urlencoded; charset=utf-8
            content-length: 13

            flower=sakura
            BODY,
            $response->getBody()->getContents()
        );
    }

    /**
     * testDownload
     *
     * @return  void
     */
    public function testDownload()
    {
        $root = vfsStream::setup(
            'root',
            0755,
            [
                'download' => []
            ]
        );

        $dest = 'vfs://root/download/downloaded.tmp';

        self::assertFileDoesNotExist($dest);

        $request = $this->createRequest(new Stream());

        $src = WINDWALKER_TEST_HTTP_URL;

        $request = $request->withUri(new Uri($src))
            ->withMethod('GET');

        $response = $this->instance->download($request, $dest);

        self::assertStringSafeEquals(
            <<<BODY
            GET http://localhost:8163/
            host: localhost:8163
            accept: */*
            content-type: application/x-www-form-urlencoded; charset=utf-8
            BODY,
            trim(file_get_contents($dest))
        );
    }
}
