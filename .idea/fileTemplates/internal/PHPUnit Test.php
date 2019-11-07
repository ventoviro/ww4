<?php declare(strict_types=1);

#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};
#end

use PHPUnit\Framework\TestCase;
use ${TESTED_NAMESPACE}\\${TESTED_NAME};

#parse("PHP Class Doc Comment.php")
class ${NAME} extends TestCase
{
    protected ?${TESTED_NAME} $instance;

    protected function setUp(): void
    {
        $this->instance = null;
    }

    protected function tearDown(): void
    {
    }
}
