<?php declare(strict_types=1);

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

namespace Windwalker\Scalars\Concern;

use Windwalker\Utilities\StrInflector;

/**
 * The StrInflectorTrait class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait StringInflectorTrait
{
    public function isPlural(): bool
    {
        return StrInflector::isPlural($this->string);
    }

    public function isSingular(): bool
    {
        return StrInflector::isSingular($this->string);
    }

    public function toPlural(): bool
    {
        return StrInflector::isPlural($this->string);
    }

    public function toSingular(): bool
    {
        return StrInflector::isSingular($this->string);
    }
}
