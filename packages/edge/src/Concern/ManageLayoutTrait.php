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
 * Trait ManageLayoutTrait
 */
trait ManageLayoutTrait
{
    /**
     * The stack of in-progress sections.
     *
     * @var array
     */
    protected array $sectionStack = [];

    /**
     * Start injecting content into a section.
     *
     * @param  string  $section
     * @param  string  $content
     *
     * @return void
     */
    public function startSection(string $section, string $content = ''): void
    {
        if ($content === '') {
            if (ob_start()) {
                $this->sectionStack[] = $section;
            }
        } else {
            $this->hasParents[$section] = str_contains($content, '@parent');

            $this->extendSection($section, $content);
        }
    }

    /**
     * Inject inline content into a section.
     *
     * @param  string  $section
     * @param  string  $content
     *
     * @return void
     */
    public function inject(string $section, string $content): void
    {
        $this->startSection($section, $content);
    }

    /**
     * Stop injecting content into a section and return its contents.
     *
     * @return string
     */
    public function yieldSection(): string
    {
        if (empty($this->sectionStack)) {
            return '';
        }

        return $this->yieldContent($this->stopSection());
    }

    /**
     * Stop injecting content into a section.
     *
     * @param  bool  $overwrite
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function stopSection(bool $overwrite = false): string
    {
        if (empty($this->sectionStack)) {
            throw new \InvalidArgumentException('Cannot end a section without first starting one.');
        }

        $last = array_pop($this->sectionStack);

        if ($overwrite) {
            $this->sections[$last] = ob_get_clean();
        } else {
            $content = ob_get_clean();

            $this->hasParents[$last] = strpos($content, '@parent') !== false;

            $this->extendSection($last, $content);
        }

        return $last;
    }

    /**
     * Stop injecting content into a section and append it.
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function appendSection(): string
    {
        if (empty($this->sectionStack)) {
            throw new \InvalidArgumentException('Cannot end a section without first starting one.');
        }

        $last = array_pop($this->sectionStack);

        if (isset($this->sections[$last])) {
            $this->sections[$last] .= ob_get_clean();
        } else {
            $this->sections[$last] = ob_get_clean();
        }

        return $last;
    }

    /**
     * Append content to a given section.
     *
     * @param  string  $section
     * @param  string  $content
     *
     * @return void
     */
    protected function extendSection(string $section, string $content): void
    {
        if (isset($this->sections[$section])) {
            $content = str_replace('@parent', $content, $this->sections[$section]);
        }

        $this->sections[$section] = $content;
    }

    /**
     * hasParent
     *
     * @param  string  $section
     *
     * @return  bool
     *
     * @since  __DEPLOY_VERSION__
     */
    public function hasParent(string $section): bool
    {
        return !empty($this->hasParents[$section]) || !isset($this->sections[$section]);
    }

    /**
     * Get the string contents of a section.
     *
     * @param  string  $section
     * @param  string  $default
     *
     * @return string
     */
    public function yieldContent(string $section, string $default = ''): string
    {
        $sectionContent = $default;

        if (isset($this->sections[$section])) {
            $sectionContent = $this->sections[$section];
            $sectionContent = str_replace('@@parent', '--parent--holder--', $sectionContent);

            return str_replace(
                '--parent--holder--',
                '@parent',
                str_replace('@parent', $default, $sectionContent)
            );
        }

        return $sectionContent;
    }

    /**
     * Flush all of the section contents.
     *
     * @return void
     */
    public function flushSections(): void
    {
        $this->renderCount = 0;

        $this->sections = [];
        $this->sectionStack = [];
        $this->hasParents = [];

        $this->pushes = [];
        $this->pushStack = [];
    }

    /**
     * Flush all of the section contents if done rendering.
     *
     * @return void
     */
    public function flushSectionsIfDoneRendering(): void
    {
        if ($this->doneRendering()) {
            $this->flushSections();
        }
    }
}
