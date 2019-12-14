<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Filesystem\Test\Path;

use PHPUnit\Framework\TestCase;
use Windwalker\Filesystem\Path\PathLocator;
use Windwalker\Filesystem\Test\FilesystemTestTrait;

/**
 * The PathLocatorTest class.
 */
class PathLocatorTest extends TestCase
{
    use FilesystemTestTrait;

    /**
     * @var PathLocator
     */
    protected $instance;

    /**
     * @see  PathLocator::isDir
     */
    public function testIsDir(): void
    {
        show($this->instance->getFileInfo()->getPathInfo()->getPathInfo());

        self::assertTrue($this->instance->isDir());
    }

    /**
     * @see  PathLocator::__toString
     */
    public function testUnderscoreToString(): void
    {
        $expt = realpath(__DIR__ . '/../files');

        self::assertEquals($expt, $this->instance->get());
    }

    /**
     * @see  PathLocator::exists
     */
    public function testExists(): void
    {
        self::assertTrue($this->instance->exists());
        self::assertFalse($this->instance->append('foo')->exists());
    }

    /**
     * @see  PathLocator::withPrefix
     */
    public function testWithPrefix(): void
    {
        $p = $this->instance->withPrefix('hello');

        $expt = 'hello' . realpath(__DIR__ . '/../files');

        self::assertEquals($expt, (string) $p);
        self::assertNotSame($p, $this->instance);
    }

    /**
     * @see  PathLocator::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(\RecursiveDirectoryIterator::class, $this->instance->getIterator());

        self::assertEquals($this->instance->getIterator()->getPath(), $this->instance->get());
    }

    /**
     * @see  PathLocator::isFile
     */
    public function testIsFile(): void
    {
        $p = new PathLocator(__DIR__ . '/../files/folder1');

        self::assertFalse($p->isFile());

        $p = new PathLocator(__DIR__ . '/../files/file1.txt');

        self::assertTrue($p->isFile());
    }

    /**
     * @see  PathLocator::append
     */
    public function testAppend(): void
    {
        $p = $this->instance->append('foo/../bar');

        self::assertPathEquals(
            dirname(__DIR__) . '/Path/../files/foo/../bar',
            $p->get(false)
        );
    }

    /**
     * @see  PathLocator::parent
     */
    public function testParent(): void
    {
        $p = $this->instance->parent('filesystem');

        self::assertEquals(dirname(__DIR__, 2), $p->get());
    }

    /**
     * @see  PathLocator::isSubdirOf
     */
    public function testIsSubdirOf(): void
    {
        self::assertTrue($this->instance->isSubdirOf(dirname(__DIR__)));
        self::assertFalse($this->instance->isSubdirOf(dirname(__DIR__) . '/foo'));
    }

    /**
     * @see  PathLocator::redirect
     */
    public function testRedirect(): void
    {
        $this->instance->redirect('/var/www');

        self::assertRealpathEquals('/var/www', $this->instance->get());
    }

    /**
     * @see  PathLocator::__construct
     */
    public function testConstruct(): void
    {
        self::assertEquals(
            __DIR__ . '/../files',
            $this->instance->get(false)
        );
    }

    /**
     * @see  PathLocator::prepend
     */
    public function testPrepend(): void
    {
        $p = $this->instance->prepend('yoo');

        self::assertNotSame($p, $this->instance);

        self::assertRealpathEquals(
            'yoo/' . dirname(__DIR__) . '/files',
            $p->get(false)
        );
    }

    protected function setUp(): void
    {
        $this->instance = new PathLocator(__DIR__ . '/../files');
    }

    protected function tearDown(): void
    {
    }
}
