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
use Windwalker\Filesystem\Stream\FsStreamWrapper;
use Windwalker\Stream\Stream;

/**
 * The FilesystemManualTest class.
 */
class FilesystemManualTest extends TestCase
{
    public function testBasicUsage(): void
    {
        self::markTestSkipped('');

        $fs = new Filesystem(__DIR__ . '/tmp');
        // $file = $fs->writeStream('foo.php', new Stream(__FILE__));
        //
        // show($file);

        $content = $fs->read('foo.php');

        show($content);
    }
}
