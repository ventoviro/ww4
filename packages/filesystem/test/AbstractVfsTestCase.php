<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Filesystem\Test;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Path;

/**
 * The AbstractFilesystemTest class.
 *
 * @since  2.0
 */
abstract class AbstractVfsTestCase extends TestCase
{
    use FilesystemTestTrait;

    /**
     * @var array
     */
    protected static $structure = [
        'files' => [
            'folder1' => [
                'level2' => [
                    'file3' => 'file3',
                ],
                'path1' => 'path1',
            ],
            'folder2' => [
                'file2.html' => 'file2.html',
            ],
            'file1.txt' => 'file1.txt',
        ],
    ];

    /**
     * Property src.
     *
     * @var string
     */
    protected static $baseDir = 'vfs://root/files';

    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    /**
     * setUpBeforeClass
     *
     * @return  void
     */
    public static function setUpBeforeClass(): void
    {
    }

    /**
     * tearDownAfterClass
     *
     * @return  void
     */
    public static function tearDownAfterClass(): void
    {
    }

    /**
     * __desctuct
     */
    public function __destruct()
    {
    }

    /**
     * setUp
     *
     * @return  void
     */
    protected function setUp(): void
    {
        $this->root = vfsStream::setup('root', null, static::$structure);
    }

    /**
     * tearDown
     *
     * @return  void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * listFiles
     *
     * @param  iterable  $files
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
     * @param  iterable  $paths
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
     * getFilesRecursive
     *
     * @return  array
     */
    public static function getFilesRecursive(): array
    {
        return [
            static::$baseDir . '/file1.txt',
            static::$baseDir . '/folder1/level2/file3',
            static::$baseDir . '/folder1/path1',
            static::$baseDir . '/folder2/file2.html',
        ];
    }

    /**
     * getFoldersRecursive
     *
     * @return  array
     */
    public static function getFoldersRecursive(): array
    {
        return [
            static::$baseDir . '/folder1',
            static::$baseDir . '/folder1/level2',
            static::$baseDir . '/folder2',
        ];
    }

    /**
     * getItemsRecursive
     *
     * @return  array
     */
    public static function getItemsRecursive(): array
    {
        return [
            static::$baseDir . '/file1.txt',
            static::$baseDir . '/folder1',
            static::$baseDir . '/folder1/level2',
            static::$baseDir . '/folder1/level2/file3',
            static::$baseDir . '/folder1/path1',
            static::$baseDir . '/folder2',
            static::$baseDir . '/folder2/file2.html',
        ];
    }
}
