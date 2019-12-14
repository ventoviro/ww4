<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Filesystem\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Filesystem\Exception\FilesystemException;
use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Filesystem;
use Windwalker\Filesystem\Path;

/**
 * The AbstractFilesystemTest class.
 *
 * @since  2.0
 */
abstract class AbstractFilesystemTest extends TestCase
{
    use FilesystemTestTrait;

    /**
     * Property dest.
     *
     * @var string
     */
    protected static $dest;

    /**
     * Property src.
     *
     * @var string
     */
    protected static $src;

    /**
     * setUpBeforeClass
     *
     * @return  void
     */
    public static function setUpBeforeClass(): void
    {
        // @mkdir(__DIR__ . '/dest');
    }

    /**
     * tearDownAfterClass
     *
     * @return  void
     */
    public static function tearDownAfterClass(): void
    {
        self::deleteDestFiles();
    }

    /**
     * __desctuct
     */
    public function __destruct()
    {
        static::deleteDestFiles();
    }

    /**
     * setUp
     *
     * @return  void
     */
    protected function setUp(): void
    {
        static::$dest = __DIR__ . '/dest';
        static::$src = __DIR__ . '/files';

        (new Filesystem())->copy(static::$src, static::$dest, true);
    }

    /**
     * tearDown
     *
     * @return  void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        static::deleteDestFiles();
    }

    protected static function deleteDestFiles(): void
    {
        if (is_dir(static::$dest)) {
            chmod(static::$dest, 0777);
            (new Filesystem())->delete(static::$dest);
        }
    }

    /**
     * listFiles
     *
     * @param iterable $files
     *
     * @return  void
     */
    public static function listFiles($files)
    {
        foreach ($files as $file) {
            echo $file . "\n";
        }
    }

    /**
     * cleanPaths
     *
     * @param iterable $paths
     *
     * @return  mixed
     */
    public static function cleanPaths($paths)
    {
        $p = [];

        foreach ($paths as $key => $path) {
            $p[$key] = Path::clean(FileObject::unwrap($path));
        }

        sort($p);

        return $p;
    }

    /**
     * getFilesRescurive
     *
     * @param string $folder
     *
     * @return  array
     */
    public static function getFilesRecursive($folder = 'dest')
    {
        return [
            __DIR__ . '/' . $folder . '/file1.txt',
            __DIR__ . '/' . $folder . '/folder1/level2/file3',
            __DIR__ . '/' . $folder . '/folder1/path1',
            __DIR__ . '/' . $folder . '/folder2/file2.html',
        ];
    }

    /**
     * getFilesRescurive
     *
     * @param string $folder
     *
     * @return  array
     */
    public static function getFoldersRecursive($folder = 'dest')
    {
        return [
            __DIR__ . '/' . $folder . '/folder1',
            __DIR__ . '/' . $folder . '/folder1/level2',
            __DIR__ . '/' . $folder . '/folder2',
        ];
    }

    /**
     * getFilesRescurive
     *
     * @param string $folder
     *
     * @return  array
     */
    public static function getItemsRecursive($folder = 'dest')
    {
        return [
            __DIR__ . '/' . $folder . '/file1.txt',
            __DIR__ . '/' . $folder . '/folder1',
            __DIR__ . '/' . $folder . '/folder1/level2',
            __DIR__ . '/' . $folder . '/folder1/level2/file3',
            __DIR__ . '/' . $folder . '/folder1/path1',
            __DIR__ . '/' . $folder . '/folder2',
            __DIR__ . '/' . $folder . '/folder2/file2.html',
        ];
    }
}
