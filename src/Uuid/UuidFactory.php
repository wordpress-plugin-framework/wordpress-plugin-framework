<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Uuid;

use WordPressPluginFramework\{
    Uuid\Uuid,
    Uuid\UuidInterface,
};

readonly class UuidFactory implements UuidFactoryInterface
{
    public function create(): UuidInterface
    {
        return new Uuid(
            wp_generate_uuid4(),
        );
    }
}
