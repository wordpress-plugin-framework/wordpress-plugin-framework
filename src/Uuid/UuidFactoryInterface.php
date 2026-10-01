<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Uuid;

use WordPressPluginFramework\Uuid\UuidInterface;

interface UuidFactoryInterface
{
    public function create(): UuidInterface;
}
