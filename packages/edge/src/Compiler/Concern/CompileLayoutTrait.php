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
 * Trait CompileLayoutTrait
 */
trait CompileLayoutTrait
{
    /**
     * Compile the extends statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileExtends(string $expression): string
    {
        $expression = $this->stripParentheses($expression);

        // @codingStandardsIgnoreStart
        $data = "<?php echo \$__edge->render($expression, \$__edge->except(get_defined_vars(), ['__data', '__path'])); ?>";
        // @codingStandardsIgnoreEnd

        $this->footer[] = $data;

        return '';
    }

    /**
     * Compile the yield statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileYield(string $expression): string
    {
        return "<?php echo \$__edge->yieldContent{$expression}; ?>";
    }

    /**
     * Compile the show statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileShow(string $expression): string
    {
        return '<?php endif; echo $__edge->yieldSection(); ?>';
    }

    /**
     * Compile the section statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileSection(string $expression): string
    {
        $params = explode(',', $expression);

        if (count($params) >= 2) {
            return "<?php \$__edge->startSection{$expression}; ?>";
        }

        return "<?php \$__edge->startSection{$expression}; if (\$__edge->hasParent{$expression}): ?>";
    }

    /**
     * Compile the append statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileAppend(string $expression): string
    {
        return '<?php endif; $__edge->appendSection(); ?>';
    }

    /**
     * Compile the end-section statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileEndsection(string $expression): string
    {
        return '<?php endif; $__edge->stopSection(); ?>';
    }

    /**
     * Compile the stop statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileStop(string $expression): string
    {
        return '<?php endif; $__edge->stopSection(); ?>';
    }

    /**
     * Compile the overwrite statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileOverwrite(string $expression): string
    {
        return '<?php endif; $__edge->stopSection(true); ?>';
    }

    /**
     * Compile the has section statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileHasSection(string $expression): string
    {
        return "<?php if (! empty(trim(\$__edge->yieldContent{$expression}))): ?>";
    }
}
