<?php declare(strict_types=1);
/**
 * Part of Windwalker project Test files.  @codingStandardsIgnoreStart
 *
 * @copyright  Copyright (C) 2019 SMS Taiwan, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Http\Test\Transport;

use Psr\Http\Message\StreamInterface;
use Windwalker\Http\Request\Request;
use Windwalker\Http\Transport\AbstractTransport;
use Windwalker\Http\Uri\Uri;
use Windwalker\Http\Uri\UriHelper;
use Windwalker\Stream\Stream;
use Windwalker\Stream\StringStream;

/**
 * Test class of CurlTransport
 *
 * @since 2.1
 */
abstract class AbstractTransportTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Property options.
     *
     * @var  array
     */
    protected $options = [
        'options' => [],
    ];

    /**
     * Test instance.
     *
     * @var AbstractTransport
     */
    protected $instance;

    /**
     * Property downloadFile.
     *
     * @var  string
     */
    protected $destFile;

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

        $this->destFile = __DIR__ . '/downloaded.tmp';
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

        $request = $request->withUri(new Uri(WINDWALKER_TEST_HTTP_URL))
            ->withMethod('GET');

        $response = $this->instance->request($request);

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($response->getBody()->getContents());

        $request = $this->createRequest();

        $request = $request->withUri(new Uri(WINDWALKER_TEST_HTTP_URL . '?foo=bar&baz[3]=yoo'))
            ->withMethod('GET');

        $response = $this->instance->request($request);

        $data = json_decode($response->getBody()->getContents(), true);
        self::assertEquals(['foo' => 'bar', 'baz' => [3 => 'yoo']], $data['_GET']);
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

        $request = $request->withUri(new Uri(dirname(WINDWALKER_TEST_HTTP_URL) . '/wrong.php'))
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

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(['foo' => 'bar'], $data['_POST']);
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

        $data = json_decode($response->getBody()->getContents(), true);

        self::assertEquals(['foo' => 'bar'], $data['data']);
        self::assertEquals('PUT', $data['_SERVER']['REQUEST_METHOD']);
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

        $data = json_decode($response->getBody()->getContents(), true);

        self::assertEquals('username', $data['_SERVER']['PHP_AUTH_USER']);
        self::assertEquals('pass1234', $data['_SERVER']['PHP_AUTH_PW']);
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

        $data = json_decode($response->getBody()->getContents(), true);

        self::assertEquals(['foo' => 'bar'], $data['_GET']);
        self::assertEquals(['flower' => 'sakura'], $data['_POST']);
    }

    /**
     * testDownload
     *
     * @return  void
     */
    public function testDownload()
    {
        $this->unlinkDownloaded();

        self::assertFileNotExists((string) $this->destFile);

        $request = $this->createRequest(new Stream());

        $src = dirname(WINDWALKER_TEST_HTTP_URL) . '/download_stub.txt';

        $request = $request->withUri(new Uri($src))
            ->withMethod('GET');

        $response = $this->instance->download($request, $this->destFile);

        self::assertEquals('This is test download file.', trim(file_get_contents($this->destFile)));
    }

    /**
     * unlinkDownloaded
     *
     * @return  void
     */
    protected function unlinkDownloaded()
    {
        if (is_file($this->destFile)) {
            @unlink($this->destFile);
        }
    }
}
