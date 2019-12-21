<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Filesystem\Test\Iterator;

use PHPUnit\Framework\TestCase;
use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Iterator\FilesIterator;
use Windwalker\Filesystem\Path;
use Windwalker\Filesystem\Test\AbstractFilesystemTest;
use Windwalker\Test\Traits\BaseAssertionTrait;
use function Windwalker\regex;

/**
 * The FilesIteratorTest class.
 */
class FilesIteratorTest extends AbstractFilesystemTest
{
    use BaseAssertionTrait;

    /**
     * Property dest.
     *
     * @var string
     */
    protected static $dest = __DIR__ . '/../dest';

    /**
     * Property src.
     *
     * @var string
     */
    protected static $src = __DIR__ . '/../files';

    /**
     * @var FilesIterator
     */
    protected $instance;

    /**
     * @see  FilesIterator::getInnerIterator
     */
    public function testIter(): void
    {
        $it = FilesIterator::create(__DIR__ . '/../dest', true);

        self::assertEquals(
            static::cleanPaths(static::getItemsRecursive('dest')),
            static::cleanPaths($it->toArray())
        );
    }

    /**
     * @see  FilesIterator::map
     */
    public function testMap(): void
    {
        $it = FilesIterator::create(__DIR__ . '/../dest');

        $it = $it->map(static function (FileObject $file) {
            return $file->getFilename();
        });

        self::assertArraySimilar(
            ['file1.txt', 'folder2', 'folder1'],
            $it->toArray()
        );
    }

    /**
     * @see  FilesIterator::filter
     */
    public function testFilter(): void
    {
        $it = FilesIterator::create(__DIR__ . '/../dest');

        $it = $it
            ->filter(static function (FileObject $file) {
                return $file->isDir();
            })
            ->map(static function (FileObject $file) {
                return $file->getFilename();
            });

        self::assertArraySimilar(
            ['folder2', 'folder1'],
            $it->toArray()
        );

        $it = FilesIterator::create(__DIR__ . '/../dest');

        $it = $it
            ->filter(regex('folder2'))
            ->map(static function (FileObject $file) {
                return $file->getFilename();
            });

        self::assertEquals(
            ['folder2'],
            $it->toArray()
        );
    }
}
