<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Edge\Compiler\Concern;

/**
 * Trait CompileRawPhpTrait
 */
trait CompileRawPhpTrait
{
    /**
     * Compile the raw PHP statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compilePhp(string $expression): string
    {
        return $expression ? "<?php {$expression}; ?>" : '<?php ';
    }

    /**
     * Compile end-php statement into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileEndphp(string $expression): string
    {
        return ' ?>';
    }

    /**
     * Compile the unset statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileUnset(string $expression): string
    {
        return "<?php unset{$expression}; ?>";
    }
}
