<?php

namespace WordPressPluginFramework\Uuid;

readonly class Uuid implements UuidInterface
{
    protected function __construct(
        protected string $uuid,
    ) {
    }

    public function __toString(): string
    {
        return $this->uuid;
    }
}