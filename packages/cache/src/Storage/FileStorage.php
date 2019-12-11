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
                'extension' => '.data',
                'expiration_format' => '/////---------- Expired At: %s ----------/////%s'
            ],
            $options
        );

        $this->checkFilePath($root);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        $data = $this->read($key);

        sscanf($this->getOption('expiration_format'), $data, $expiration, $value);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        if (!$this->exists($key)) {
            return false;
        }

        $data = $this->read($key);

        sscanf($this->getOption('expiration_format'), $data, $expiration);

        if ($expiration > time()) {
            return true;
        }

        $this->remove($key);

        return false;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $filePath = $this->root;
        $this->checkFilePath($filePath);

        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($filePath)
            ),
            '/' . preg_quote($this->getOption('extension')) . '$/i'
        );

        $results = true;

        /* @var  \RecursiveDirectoryIterator $file */
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $results = unlink($file->getRealPath()) && $results;
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): bool
    {
        return unlink($this->fetchStreamUri($key));
    }

    /**
     * @inheritDoc
     */
    public function save(string $key, $value, int $expiration = 0): bool
    {
        if ($this->getOption('deny_access', false)) {
            $value = $this->getOption('deny_code') . $value;
        }

        $expirationFormat = $this->getOption('expiration_format');

        $value = sprintf($expirationFormat, $expiration, $value);

        return $this->write($key, $value);
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
     * @param  string  $key
     * @param  string  $value
     *
     * @return  boolean
     */
    protected function write(string $key, string $value): bool
    {
        $filename = $this->fetchStreamUri($key);

        return (bool) file_put_contents(
            $filename,
            $value,
            ($this->getOption('lock', false) ? LOCK_EX : null)
        );
    }

    /**
     * read
     *
     * @param  string  $key
     *
     * @return  string
     */
    protected function read(string $key): string
    {
        $filename = $this->fetchStreamUri($key);

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
     * exists
     *
     * @param  string  $key
     *
     * @return  bool
     */
    protected function exists(string $key): bool
    {
        return is_file($this->fetchStreamUri($key));
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
