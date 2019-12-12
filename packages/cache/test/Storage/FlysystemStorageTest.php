<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Test\Storage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Windwalker\Cache\Storage\FlysystemStorage;

/**
 * The FileStorageTest class.
 */
class FlysystemStorageTest extends FileStorageTest
{
    /**
     * @var FlysystemStorage
     */
    protected $instance;

    /**
     * setUp
     *
     * @return  void
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    protected function setUp(): void
    {
        $this->root = $path = dirname(__DIR__) . '/fixtures/';

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $fly = new Filesystem(new Local($path));

        $this->instance = new FlysystemStorage($fly);

        $this->instance->clear();
    }

    protected function tearDown(): void
    {
        $this->instance->clear();
    }
}
