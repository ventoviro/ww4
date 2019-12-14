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
use Windwalker\Filesystem\Filesystem;

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
        self::markTestIncomplete(); // TODO: Complete this test
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
     * @see  Filesystem::iteratorToArray
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
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  Filesystem::move
     */
    public function testMove(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
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

        $this->assertInstanceOf(\CallbackFilterIterator::class, $items);

        $items2 = Filesystem::iteratorToArray($items);

        $this->assertEquals(
            static::cleanPaths($compare),
            static::cleanPaths($items2)
        );

        $items->rewind();

        $this->assertInstanceOf(\SplFileInfo::class, $items->current());
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
        self::markTestIncomplete(); // TODO: Complete this test
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
        self::markTestIncomplete(); // TODO: Complete this test
    }
}
