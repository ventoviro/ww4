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
 * Trait CompileCommentTrait
 */
trait CompileCommentTrait
{
    /**
     * Compile Blade comments into valid PHP.
     *
     * @param  string $value
     *
     * @return string
     */
    protected function compileComments(string $value): string
    {
        $pattern = sprintf('/%s--(.*?)--%s/s', $this->contentTags[0], $this->contentTags[1]);

        return preg_replace($pattern, '<?php /*$1*/ ?>', $value);
    }
}
