<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Exceptions\Views;

use WordPressPluginFramework\{
    Exceptions\Interfaces\HasMessagesInterface,
    Renderer\RenderableInterface,
};
use Throwable;

readonly class View implements RenderableInterface
{
    protected function __construct(
        public string $message,
        public string $code,
        public ?array $messages,
    ) {
    }

    public static function createFromThrowable(Throwable $throwable): static
    {
        $message = $throwable->getMessage();
        $code = (string) $throwable->getCode();
        $messages = $throwable instanceof HasMessagesInterface ? $throwable->getMessages() : null;

        return new static($message, $code, $messages);
    }

    public function view(): string {
        return '';
    }
}