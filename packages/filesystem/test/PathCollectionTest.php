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
use Windwalker\Filesystem\PathCollection;

/**
 * The PathCollectionTest class.
 */
class PathCollectionTest extends AbstractVfsTestCase
{
    /**
     * @var PathCollection
     */
    protected $instance;

    /**
     * @see  PathCollection::isChild
     */
    public function testIsChild(): void
    {
        $p = new PathCollection([
            'vfs://foo/bar/yoo',
            'vfs://foo/goo'
        ]);

        self::assertTrue($p->isChild('vfs://foo/goo/joo'));
        self::assertFalse($p->isChild('vfs://foo/tu'));
    }

    /**
     * @see  PathCollection::addPaths
     */
    public function testAddPaths(): void
    {
        $p = new PathCollection([
            'vfs://foo/bar/yoo',
        ]);

        $p2 = $p->addPaths([
            'vfs://flower/sakura',
            'vfs://foo/goo'
        ]);

        self::assertEquals(
            [
                'vfs://foo/bar/yoo',
                'vfs://flower/sakura',
                'vfs://foo/goo',
            ],
            $p2->toArray()
        );
        self::assertNotSame($p, $p2);
    }

    /**
     * @see  PathCollection::add
     */
    public function testAdd(): void
    {
        $p = new PathCollection([
            'vfs://foo/bar/yoo',
        ]);

        $p2 = $p->add('vfs://flower/sakura');

        self::assertEquals(
            [
                'vfs://foo/bar/yoo',
                'vfs://flower/sakura',
            ],
            $p2->toArray()
        );
        self::assertNotSame($p, $p2);
    }

    /**
     * @see  PathCollection::appendAll
     */
    public function testAppendAll(): void
    {
        $p = new PathCollection([
            'foo/bar/yoo',
            'flower/sakura',
            'foo/goo/joo',
        ]);

        $p2 = $p->appendAll('/../');

        self::assertEquals(
            [
                'foo/bar/yoo/..',
                'flower/sakura/..',
                'foo/goo/joo/..',
            ],
            $p2->toArray()
        );
        self::assertNotSame($p, $p2);
    }

    /**
     * @see  PathCollection::prependAll
     */
    public function testPrependAll(): void
    {
        $p = new PathCollection([
            'foo/bar/yoo',
            'flower/sakura',
            'foo/goo/joo',
        ]);

        $p2 = $p->prependAll('vfs://');

        self::assertEquals(
            [
                'vfs://foo/bar/yoo',
                'vfs://flower/sakura',
                'vfs://foo/goo/joo',
            ],
            $p2->toArray()
        );
        self::assertNotSame($p, $p2);
    }

    /**
     * @see  PathCollection::getPaths
     */
    public function testGetPaths(): void
    {
        $p = new PathCollection([
            'foo/bar/yoo',
            'flower/sakura',
            'foo/goo/joo',
        ]);

        self::assertEquals(
            [
                'foo/bar/yoo',
                'flower/sakura',
                'foo/goo/joo',
            ],
            array_map('strval', $p->getPaths())
        );
    }

    /**
     * @see  PathCollection::withPaths
     */
    public function testWithPaths(): void
    {
        $p = new PathCollection([
            'vfs://foo/bar/yoo',
        ]);

        $p2 = $p->withPaths([
            'vfs://flower/sakura',
            'vfs://foo/goo'
        ]);

        self::assertEquals(
            [
                'vfs://flower/sakura',
                'vfs://foo/goo',
            ],
            $p2->toArray()
        );
        self::assertNotSame($p, $p2);
    }

    /**
     * @see  PathCollection::items
     */
    public function testItems(): void
    {
        $p = new PathCollection([
            'vfs://root/files/folder1',
            'vfs://root/files/folder2',
        ]);

        show($p->items(true)->toArray());
    }

    /**
     * @see  PathCollection::files
     */
    public function testFiles(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PathCollection::folders
     */
    public function testFolders(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PathCollection::getPath
     */
    public function testGetPath(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    /**
     * @see  PathCollection::map
     */
    public function testMap(): void
    {
        self::markTestIncomplete(); // TODO: Complete this test
    }

    protected function setUp(): void
    {
        $this->instance = null;
    }

    protected function tearDown(): void
    {
    }
}
