<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Edge\Concern;

/**
 * Trait ManageStackTrait
 */
trait ManageStackTrait
{
    /**
     * The stack of in-progress push sections.
     *
     * @var array
     */
    protected array $pushStack = [];

    /**
     * Start injecting content into a push section.
     *
     * @param  string $section
     * @param  string $content
     *
     * @return void
     */
    public function startPush(string $section, string $content = '')
    {
        if ($content === '') {
            if (ob_start()) {
                $this->pushStack[] = $section;
            }
        } else {
            $this->extendPush($section, $content);
        }
    }

    /**
     * Stop injecting content into a push section.
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function stopPush(): string
    {
        if (empty($this->pushStack)) {
            throw new \InvalidArgumentException('Cannot end a section without first starting one.');
        }

        $last = array_pop($this->pushStack);

        $this->extendPush($last, ob_get_clean());

        return $last;
    }

    /**
     * Append content to a given push section.
     *
     * @param  string  $section
     * @param  string  $content
     *
     * @return void
     */
    protected function extendPush(string $section, string $content): void
    {
        if (!isset($this->pushes[$section])) {
            $this->pushes[$section] = [];
        }

        if (!isset($this->pushes[$section][$this->renderCount])) {
            $this->pushes[$section][$this->renderCount] = $content;
        } else {
            $this->pushes[$section][$this->renderCount] .= $content;
        }
    }

    /**
     * Get the string contents of a push section.
     *
     * @param  string  $section
     * @param  string  $default
     *
     * @return string
     */
    public function yieldPushContent(string $section, string $default = ''): string
    {
        if (!isset($this->pushes[$section])) {
            return $default;
        }

        return implode(array_reverse($this->pushes[$section]));
    }
}
