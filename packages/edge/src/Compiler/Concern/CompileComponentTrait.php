<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Edge\Compiler\Concern;

/**
 * The CompileComponentTrait class.
 *
 * @since  3.3.1
 */
trait CompileComponentTrait
{
    /**
     * Compile the component statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileComponent(string $expression): string
    {
        return "<?php \$this->startComponent{$expression}; ?>";
    }

    /**
     * Compile the end-component statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndComponent(): string
    {
        return '<?php echo $this->renderComponent(); ?>';
    }

    /**
     * Compile the slot statements into valid PHP.
     *
     * @param  string $expression
     *
     * @return string
     */
    protected function compileSlot(string $expression): string
    {
        return "<?php \$this->slot{$expression}; ?>";
    }

    /**
     * Compile the end-slot statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndSlot(): string
    {
        return '<?php $this->endSlot(); ?>';
    }
}
