<?php

/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT Taiwan, Inc.
 * @license    LGPL-2.0-or-later
 */

declare(strict_types=1);

namespace Windwalker\Filesystem\Test;

use PHPUnit\Framework\TestCase;
use Windwalker\Filesystem\Path;

/**
 * Test class of Path
 *
 * @since 2.0
 */
class PathTest extends TestCase
{
    /**
     * Data provider for testClean() method.
     *
     * @return  array
     *
     * @since   2.0
     */
    public function cleanProvider(): array
    {
        return [
            // Input Path, Directory Separator, Expected Output
            'Nothing to do.' => ['/var/www/foo/bar/baz', '/', '/var/www/foo/bar/baz'],
            'One backslash.' => ['/var/www/foo\\bar/baz', '/', '/var/www/foo/bar/baz'],
            'Two and one backslashes.' => ['/var/www\\\\foo\\bar/baz', '/', '/var/www/foo/bar/baz'],
            'Mixed backslashes and double forward slashes.' => [
                '/var\\/www//foo\\bar/baz',
                '/',
                '/var/www/foo/bar/baz',
            ],
            'UNC path.' => ['\\\\www\\docroot', '\\', '\\\\www\\docroot'],
            'UNC path with forward slash.' => ['\\\\www/docroot', '\\', '\\\\www\\docroot'],
            'UNC path with UNIX directory separator.' => ['\\\\www/docroot', '/', '/www/docroot'],
            'Stream URL.' => ['vfs://files//foo\\bar', '/', 'vfs://files/foo/bar'],
            'Stream URL empty.' => ['vfs://', '/', 'vfs://'],
            'Windows path.' => ['C:\\files\\\\foo//bar', '\\', 'C:\\files\\foo\\bar'],
            'Windows path empty.' => ['C:\\', '\\', 'C:\\'],
        ];
    }

    /**
     * Method to test setPermissions().
     *
     * @return void
     *
     * @covers \Windwalker\Filesystem\Path::setPermissions
     * @TODO   Implement testSetPermissions().
     */
    public function testSetPermissions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test getPermissions().
     *
     * @return void
     *
     * @covers \Windwalker\Filesystem\Path::getPermissions
     * @TODO   Implement testGetPermissions().
     */
    public function testGetPermissions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Method to test clean().
     *
     * @param   string $input
     * @param   string $ds
     * @param   string $expected
     *
     * @return void
     *
     * @covers        \Windwalker\Filesystem\Path::clean
     *
     * @dataProvider  cleanProvider
     */
    public function testClean($input, $ds, $expected)
    {
        $this->assertEquals(
            $expected,
            Path::clean($input, $ds)
        );
    }

    /**
     * testExistsInsensitive
     *
     * @param string  $path
     * @param bool    $sExists
     * @param bool    $iExists
     *
     * @return void
     * @dataProvider existsProvider
     */
    public function testExists($path, $sExists, $iExists)
    {
        self::assertSame($sExists, Path::exists($path, Path::CASE_SENSITIVE));
        self::assertSame($iExists, Path::exists($path, Path::CASE_INSENSITIVE));
    }

    /**
     * existsProvider
     *
     * @return  array
     */
    public function existsProvider(): array
    {
        return [
            [
                __DIR__ . '/case/Flower/saKura/test.txt',
                false,
                true,
            ],
            [
                __DIR__ . '/case/Flower/saKura/TEST.txt',
                true,
                true,
            ],
            [
                __DIR__ . '/case/Flower/sakura',
                false,
                true,
            ],
            [
                __DIR__ . '/case/Flower/Olive',
                false,
                false,
            ],
            [
                'vfs://root/files',
                true,
                true,
            ],
        ];
    }

    /**
     * testFixCase
     *
     * @return  void
     */
    public function testFixCase()
    {
        $path = __DIR__ . '/case/Flower/saKura/test.txt';

        self::assertEquals(Path::clean(__DIR__ . '/case/Flower/saKura/TEST.txt'), Path::fixCase($path));
    }
}
