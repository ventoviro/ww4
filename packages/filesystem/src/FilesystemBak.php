<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Filesystem;

use League\Flysystem\Adapter\CanOverwriteFiles;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\RootViolationException;
use League\Flysystem\Util;
use Psr\Http\Message\StreamInterface;
use Windwalker\Filesystem\Stream\FsStreamWrapper;

/**
 * The Filesystem class.
 */
class FilesystemBak
{
    /**
     * @var Flysystem
     */
    protected $fs;

    /**
     * Filesystem constructor.
     *
     * @param  Flysystem|AdapterInterface|string  $source
     * @param  array                              $options
     */
    public function __construct($source = null, array $options = [])
    {
        $this->setSource($source, $options);
    }

    /**
     * has
     *
     * @param  string  $path
     *
     * @return  bool
     */
    public function has(string $path): bool
    {
        return (bool) $this->getAdapter()->has(Path::normalize($path));
    }

    public function write(string $path, string $contents, array $options = [])
    {
        return $this->wrapFile(
            $this->getAdapter()->write(Path::normalize($path), $contents, $this->prepareConfig($options))
        );
    }

    public function writeStream(string $path, $resource, array $options = []): FileObject
    {
        return $this->wrapFile(
            $this->getAdapter()
                ->writeStream(Path::normalize($path), $this->wrapResource($resource), $this->prepareConfig($options))
        );
    }

    public function put(string $path, string $contents, array $options = []): bool
    {
        $path = Path::normalize($path);

        if (!$this->getAdapter() instanceof CanOverwriteFiles && $this->has($path)) {
            return (bool) $this->getAdapter()->update($path, $contents, $this->prepareConfig($options));
        }

        return (bool) $this->getAdapter()
            ->write($path, $contents, $this->prepareConfig($options));
    }

    public function putStream(string $path, $resource, array $options = []): bool
    {
        $resource = $this->wrapResource($resource);

        if (!$this->getAdapter() instanceof CanOverwriteFiles && $this->has($path)) {
            return (bool) $this->getAdapter()
                ->updateStream(Path::normalize($path), $this->wrapResource($resource), $this->prepareConfig($options));
        }

        return (bool) $this->getAdapter()
            ->writeStream(Path::normalize($path), $this->wrapResource($resource), $this->prepareConfig($options));
    }

    public function update(string $path, string $contents, array $options = []): bool
    {
        return (bool) $this->getAdapter()
            ->update(Path::normalize($path), $contents, $this->prepareConfig($options));
    }

    public function updateStream(string $path, $resource, array $options = []): bool
    {
        return (bool) $this->getAdapter()
            ->updateStream(Path::normalize($path), $this->wrapResource($resource), $this->prepareConfig($options));
    }

    public function read($path)
    {
        if (!$result = $this->getAdapter()->read(Path::normalize($path))) {
            return false;
        }

        return $result['contents'];
    }

    public function readStream($path)
    {
        $path = Path::normalize($path);

        if (!$object = $this->getAdapter()->readStream($path)) {
            return false;
        }

        return $object['stream'];
    }

    public function delete($path)
    {
        return $this->getAdapter()->delete(Path::normalize($path));
    }

    public function readAndDelete(string $path)
    {
        $contents = $this->read($path);

        if ($contents === false) {
            return false;
        }

        $this->delete($path);

        return $contents;
    }

    public function deleteDir($dirname): bool
    {
        $dirname = Path::normalize($dirname);

        if ($dirname === '') {
            throw new RootViolationException('Root directories can not be deleted.');
        }

        return (bool) $this->getAdapter()->deleteDir($dirname);
    }

    public function createDir(string $dirname, array $options = []): bool
    {
        $dirname = Path::normalize($dirname);

        return (bool) $this->getAdapter()->createDir($dirname, $this->prepareConfig($options));
    }

    public function rename(string $path, string $newpath): bool
    {
        return (bool) $this->getAdapter()->rename(Path::normalize($path), Path::normalize($newpath));
    }

    public function copy(string $path, string $newpath)
    {
        return $this->getAdapter()->copy($path, $newpath);
    }

    public function listContents($directory = '', $recursive = false): \Traversable
    {
        $directory = Path::normalize($directory);
        $contents  = $this->getAdapter()->listContents($directory, $recursive);

        foreach ($contents as $content) {
            yield $this->wrapFile($content);
        }
    }

    /**
     * @inheritdoc
     */
    public function getMimetype($path)
    {
        $path = Path::normalize($path);

        if ((!$object = $this->getAdapter()->getMimetype($path)) || !array_key_exists('mimetype', $object)) {
            return false;
        }

        return $object['mimetype'];
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        $path = Path::normalize($path);

        if ((!$object = $this->getAdapter()->getTimestamp($path)) || !array_key_exists('timestamp', $object)) {
            return false;
        }

        return $object['timestamp'];
    }

    /**
     * @inheritdoc
     */
    public function getVisibility($path)
    {
        $path = Path::normalize($path);

        if ((!$object = $this->getAdapter()->getVisibility($path)) || !array_key_exists('visibility', $object)) {
            return false;
        }

        return $object['visibility'];
    }

    /**
     * @inheritdoc
     */
    public function getSize($path)
    {
        $path = Path::normalize($path);

        if ((!$object = $this->getAdapter()->getSize($path)) || !array_key_exists('size', $object)) {
            return false;
        }

        return (int) $object['size'];
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($path, $visibility)
    {
        $path = Path::normalize($path);

        return (bool) $this->getAdapter()->setVisibility($path, $visibility);
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        $path = Path::normalize($path);

        return $this->getAdapter()->getMetadata($path);
    }

    /**
     * @inheritdoc
     */
    public function get($path)
    {
        $path = Path::normalize($path);

        return new FileObject($this, $path);
    }

    /**
     * getFlysystem
     *
     * @return  FilesystemInterface
     */
    public function getFlysystem(): FilesystemInterface
    {
        return $this->fs;
    }

    /**
     * setSource
     *
     * @param  Flysystem|AdapterInterface|string  $source
     * @param  array                              $options
     *
     * @return  static
     */
    public function setSource($source = null, array $options = [])
    {
        if (is_string($source)) {
            $fs = new Flysystem(new Local($source), $options);
        } elseif ($source instanceof AdapterInterface) {
            $fs = new Flysystem($source, $options);
        } elseif ($source instanceof Flysystem) {
            $fs = $source;
        } else {
            $fs = new Flysystem(new Local('/'), $options);
        }

        $this->fs = $fs;

        return $this;
    }

    /**
     * getAdapter
     *
     * @return  AdapterInterface
     */
    public function getAdapter(): AdapterInterface
    {
        return $this->fs->getAdapter();
    }

    /**
     * Convert a config array to a Config object with the correct fallback.
     *
     * @param  array  $config
     *
     * @return Config
     */
    protected function prepareConfig(array $config): Config
    {
        $configObject = new Config($config);
        $configObject->setFallback($this->fs->getConfig());

        return $configObject;
    }

    /**
     * wrapFile
     *
     * @param  array|false  $metadata
     *
     * @return  FileObject|false
     */
    protected function wrapFile($metadata)
    {
        if ($metadata === false) {
            return false;
        }

        return new FileObject($this, $metadata['path']);
    }

    /**
     * Re-wrap PSR StreamInterface as a native resource.
     *
     * @param  resource|StreamInterface  $stream
     *
     * @return  resource
     */
    private function wrapResource($stream)
    {
        if ($stream instanceof StreamInterface) {
            $path = md5(uniqid('FS', true));

            FsStreamWrapper::addStream($path, $stream);

            FsStreamWrapper::register();

            return fopen(FsStreamWrapper::fetchUri($path), 'rb+');
        }

        rewind($stream);

        return $stream;
    }
}
