<?php

namespace WordPressPluginFramework\Uuid;

use WordPressPluginFramework\Uuid\UuidInterface;

interface UuidFactoryInterface
{
    public function create(): UuidInterface;
}
