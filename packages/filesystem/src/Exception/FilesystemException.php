<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Filesystem\Exception;

use Throwable;

/**
 * Exception class for handling errors in the Filesystem package
 *
 * @since  2.0
 */
class FilesystemException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @inheritDoc
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null, ?string $path = null)
    {
        parent::__construct($message, $code, $previous);

        $this->path = $path;
    }

    /**
     * Method to get property Dest
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
