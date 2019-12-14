<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Filesystem\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Filesystem;

/**
 * The FilesystemTest class.
 */
class FilesystemOriginLocalTest extends TestCase
{
    /**
     * @var Filesystem
     */
    protected $instance;

    /**
     * @see  Filesystem::get
     */
    public function testGet(): void
    {
        $fs = new Filesystem(__DIR__ . '/files');

        self::assertInstanceOf(FileObject::class, $fs->get(''));
        self::assertTrue($fs->get('')->isDir());

        $file1 = $fs->get('file1.txt');

        self::assertTrue($file1->isFile());
        self::assertEquals('file1.txt', trim($file1->read()));
    }

    /**
     * @see  Filesystem::listContents
     */
    public function testListContents(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::rename
     */
    public function testRename(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::putStream
     */
    public function testPutStream(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::update
     */
    public function testUpdate(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::setSource
     */
    public function testSetSource(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::has
     */
    public function testHas(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::getMetadata
     */
    public function testGetMetadata(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::write
     */
    public function testWrite(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::createDir
     */
    public function testCreateDir(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::getTimestamp
     */
    public function testGetTimestamp(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::getMimetype
     */
    public function testGetMimetype(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::getAdapter
     */
    public function testGetAdapter(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::getFlysystem
     */
    public function testGetFlysystem(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::writeStream
     */
    public function testWriteStream(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::setVisibility
     */
    public function testSetVisibility(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::copy
     */
    public function testCopy(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::__construct
     */
    public function test__construct(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::getVisibility
     */
    public function testGetVisibility(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::delete
     */
    public function testDelete(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::getSize
     */
    public function testGetSize(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::put
     */
    public function testPut(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::read
     */
    public function testRead(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::updateStream
     */
    public function testUpdateStream(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::readAndDelete
     */
    public function testReadAndDelete(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::deleteDir
     */
    public function testDeleteDir(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = new Filesystem(__DIR__ . '/files');
    }

    protected function tearDown(): void
    {
    }
}
