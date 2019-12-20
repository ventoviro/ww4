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
use Windwalker\Filesystem\Exception\FileNotFoundException;
use Windwalker\Filesystem\Exception\FilesystemException;
use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Filesystem;
use Windwalker\Filesystem\Iterator\FilesIterator;

/**
 * The FilesystemTest class.
 */
class FilesystemTest extends AbstractFilesystemTest
{
    use FilesystemTestTrait;

    /**
     * @var Filesystem
     */
    protected $instance;

    /**
     * @see  Filesystem::globAll
     */
    public function testGlobAll(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::findOne
     */
    public function testFindOne(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::findByCallback
     */
    public function testFindByCallback(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::delete
     */
    public function testDelete(): void
    {
        $fs = new Filesystem();

        $fs->delete(static::$dest);

        $this->assertDirectoryNotExists(static::$dest);
        $this->assertFileNotExists(static::$dest . '/folder1/level2/file3');

        restore_error_handler();

        // Delete non-exists folders
        try {
            $fs->delete(static::$dest . '/hello/no/exists');
        } catch (FilesystemException $e) {
            self::assertInstanceOf(FilesystemException::class, $e);
        }

        // Delete no-permissions folders
        if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
            $dir = __DIR__ . '/dest';
            $fs->mkdir($dir);
            chmod($dir, 0000);

            try {
                $fs->delete($dir);
            } catch (FilesystemException $e) {
                self::assertInstanceOf(FilesystemException::class, $e);
            }

            chmod($dir, 0777);
        }
    }

    /**
     * @see  Filesystem::mkdir
     */
    public function testMkdir(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::find
     */
    public function testFind(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::toArray
     */
    public function testIteratorToArray(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::copy
     */
    public function testCopy(): void
    {
        $fs = new Filesystem();

        $fs->delete(static::$dest);

        $fs->copy(static::$src, static::$dest);

        $this->assertDirectoryExists(static::$dest);
        $this->assertFileExists(__DIR__ . '/dest/folder1/level2/file3');
    }

    /**
     * @see  Filesystem::move
     */
    public function testMove(): void
    {
        $fs = new Filesystem();

        $dest2 = __DIR__ . '/dest2';

        if (is_dir($dest2)) {
            $fs->delete($dest2);
        }

        $fs->move(static::$dest, $dest2);

        $this->assertDirectoryExists($dest2);
        $this->assertFileExists($dest2 . '/folder1/level2/file3');

        $fs->delete($dest2);
    }

    /**
     * @see  Filesystem::items
     */
    public function testItems(): void
    {
        $fs = new Filesystem();

        $items = $fs->items(static::$dest . '/folder1/level2', true);

        $this->assertEquals(
            static::cleanPaths([static::$dest . '/folder1/level2/file3']),
            static::cleanPaths($items)
        );

        // Recursive
        $items = $fs->items(static::$dest, true);

        $compare = static::getItemsRecursive('dest');

        $this->assertEquals(
            static::cleanPaths($compare),
            static::cleanPaths($items)
        );

        // Iterator
        $items = $fs->items(static::$dest, true);

        $this->assertInstanceOf(FilesIterator::class, $items);

        $items2 = Filesystem::toArray($items);

        $this->assertEquals(
            static::cleanPaths($compare),
            static::cleanPaths($items2)
        );

        $items->rewind();

        $this->assertInstanceOf(\SplFileInfo::class, $items->current());

        // list non-exists folder
        restore_error_handler();

        $this->expectException(FileNotFoundException::class);

        $items = $fs->items(__DIR__ . '/not/exists');
    }

    /**
     * @see  Filesystem::glob
     */
    public function testGlob(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::folders
     */
    public function testFolders(): void
    {
        $fs = new Filesystem();

        $folders = $fs->folders(static::$dest . '/folder1', true);

        $this->assertEquals(
            static::cleanPaths([static::$dest . '/folder1/level2']),
            static::cleanPaths($folders)
        );

        // Recursive
        $folders = $fs->folders(static::$dest, true);

        $compare = static::getFoldersRecursive('dest');

        $this->assertEquals(
            static::cleanPaths($compare),
            static::cleanPaths($folders)
        );

        // Iterator
        $folders = $fs->folders(static::$dest, true);

        $this->assertInstanceOf(FilesIterator::class, $folders);

        $folders2 = Filesystem::toArray($folders);

        $this->assertEquals(
            static::cleanPaths($compare),
            static::cleanPaths($folders2)
        );

        $folders = $fs->folders(static::$dest, true);

        $this->assertInstanceOf(FileObject::class, $folders->current());
    }

    /**
     * @see  Filesystem::createIterator
     */
    public function testCreateIterator(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::files
     */
    public function testFiles(): void
    {
        $fs = new Filesystem();

        $files = $fs->files(__DIR__ . '/dest/folder1/level2', true);

        $this->assertEquals(
            static::cleanPaths([__DIR__ . '/dest/folder1/level2/file3']),
            static::cleanPaths($files)
        );

        // Recursive
        $files = $fs->files(static::$dest, true);

        $compare = static::getFilesRecursive('dest');

        $this->assertEquals(
            static::cleanPaths($compare),
            static::cleanPaths($files)
        );

        // Iterator
        $files = $fs->files(static::$dest, true);

        $this->assertInstanceOf(FilesIterator::class, $files);

        $files2 = Filesystem::toArray($files);

        $this->assertEquals(
            static::cleanPaths($compare),
            static::cleanPaths($files2)
        );

        $files->rewind();

        $this->assertInstanceOf(FileObject::class, $files->current());
    }
}
