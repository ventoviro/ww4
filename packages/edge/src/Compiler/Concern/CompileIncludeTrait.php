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
 * The CompileIncludeTrait class.
 */
trait CompileIncludeTrait
{
    /**
     * Compile the each statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileEach(string $expression): string
    {
        return "<?php echo \$__edge->renderEach{$expression}; ?>";
    }

    /**
     * Compile the include statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileInclude(string $expression): string
    {
        $expression = $this->stripParentheses($expression);

        // @codingStandardsIgnoreStart
        return "<?php echo \$__edge->render($expression, \$__edge->except(get_defined_vars(), ['__data', '__path'])); ?>";
        // @codingStandardsIgnoreEnd
    }

    /**
     * Compile the include statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileIncludeIf(string $expression): string
    {
        $expression = $this->stripParentheses($expression);

        // @codingStandardsIgnoreStart
        return "<?php if (\$__edge->exists($expression)) echo \$__edge->render($expression, \$__edge->except(get_defined_vars(), ['__data', '__path'])); ?>";
        // @codingStandardsIgnoreEnd
    }
}
