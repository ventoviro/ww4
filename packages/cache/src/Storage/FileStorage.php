<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Cache\Storage;

use RuntimeException;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The FilesystemStorage class.
 */
class FileStorage implements StorageInterface
{
    use OptionAccessTrait;

    /**
     * @var string
     */
    protected $root;

    /**
     * AbstractFormatterStorage constructor.
     *
     * @param  string  $root
     * @param  array   $options
     */
    public function __construct(string $root, array $options = [])
    {
        $this->root = $root;

        $this->prepareOptions(
            [
                'lock' => false,
                'extension' => '.data'
            ],
            $options
        );

        $this->checkFilePath($root);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, array $options = [])
    {
        return $this->read($this->fetchStreamUri($key));
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return is_file($this->fetchStreamUri($key));
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $filePath = $this->root;
        $this->checkFilePath($filePath);

        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($filePath)
            ),
            '/' . preg_quote($this->getOption('extension')) . '$/i'
        );

        /* @var  \RecursiveDirectoryIterator $file */
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                unlink($file->getRealPath());
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): void
    {
        unlink($this->fetchStreamUri($key));
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, array $options = []): void
    {
        $fileName = $this->fetchStreamUri($key);

        $filePath = pathinfo($fileName, PATHINFO_DIRNAME);

        if (!is_dir($filePath)) {
            if (!mkdir($filePath, 0770, true) && !is_dir($filePath)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $filePath));
            }
        }

        if ($this->getOption('deny_access', false)) {
            $value = $this->getOption('deny_code') . $value;
        }

        $this->write(
            $fileName,
            $value,
            ($this->getOption('lock', false) ? LOCK_EX : null)
        );
    }

    /**
     * Check that the file path is a directory and writable.
     *
     * @param  string  $filePath  A file path.
     *
     * @return  boolean  The method will always return true, if it returns.
     *
     * @throws  RuntimeException if the file path is invalid.
     * @since   2.0
     */
    protected function checkFilePath($filePath): bool
    {
        if (!is_dir($filePath)) {
            if (!mkdir($filePath, 0755, true) && !is_dir($filePath)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $filePath));
            }
        }

        if (!is_writable($filePath)) {
            throw new RuntimeException(sprintf('The base cache path `%s` is not writable.', $filePath));
        }

        return true;
    }

    /**
     * write
     *
     * @param  string  $filename
     * @param  string  $value
     * @param  int     $flags
     *
     * @return  boolean
     */
    protected function write(string $filename, string $value, int $flags): bool
    {
        return (bool) file_put_contents(
            $filename,
            $value,
            $flags
        );
    }

    /**
     * read
     *
     * @param  string  $filename
     *
     * @return  string
     */
    protected function read(string $filename): string
    {
        $resource = @fopen($filename, 'rb');

        if (!$resource) {
            throw new RuntimeException(
                sprintf(
                    'Unable to fetch cache entry for %s.  Connot open the resource.',
                    $filename
                )
            );
        }

        // If locking is enabled get a shared lock for reading on the resource.
        if ($this->getOption('lock', false) && !flock($resource, LOCK_SH)) {
            throw new RuntimeException(
                sprintf(
                    'Unable to fetch cache entry for %s.  Connot obtain a lock.',
                    $filename
                )
            );
        }

        $data = stream_get_contents($resource);

        // If locking is enabled release the lock on the resource.
        if ($this->getOption('lock', false) && !flock($resource, LOCK_UN)) {
            throw new RuntimeException(
                sprintf(
                    'Unable to fetch cache entry for %s.  Connot release the lock.',
                    $filename
                )
            );
        }

        fclose($resource);

        return $data;
    }

    /**
     * Get the full stream URI for the cache entry.
     *
     * @param  string  $key  The storage entry identifier.
     *
     * @return  string  The full stream URI for the cache entry.
     *
     * @throws  \RuntimeException if the cache path is invalid.
     * @since   2.0
     */
    public function fetchStreamUri(string $key): string
    {
        $filePath = $this->root;

        $this->checkFilePath($filePath);

        if ($this->getOption('deny_access', false)) {
            $this->options['extension'] = '.php';
        }

        return sprintf(
            '%s/~%s' . $this->getOption('extension'),
            $filePath,
            hash('sha1', $key)
        );
    }
}
