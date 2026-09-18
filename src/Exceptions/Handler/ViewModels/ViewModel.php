<?php

namespace WordPressPluginFramework\Exceptions\Handler\ViewModels;

use WordPressPluginFramework\{
    Exceptions\Interfaces\HasMessagesInterface,
    View\Model\ModelInterface as ViewModelInterface
};
use Throwable;

readonly class ViewModel implements ViewModelInterface
{
    protected function __construct(
        public string $message,
        public string $code,
        public ?array $messages,
        public ?Debug\ViewModel $debug,
    ) {
    }

    public static function createFromThrowable(Throwable $throwable): static
    {
        return new static(
            $throwable->getMessage(),
            $throwable->getCode(),
            self::getMessages($throwable),
            self::getDebug($throwable),
        );
    }

    protected static function getMessages(Throwable $throwable): ?array
    {
        if (!$throwable instanceof HasMessagesInterface) {
            return null;
        }

        return $throwable->getMessages();
    }

    protected static function getDebug(Throwable $throwable): ?Debug\ViewModel
    {
        if (
            !defined('WP_DEBUG') ||
            !WP_DEBUG
        ) {
            return null;
        }

        return Debug\ViewModel::createFromThrowable($throwable);
    }

    public function toArray(): array
    {
        $viewModel = [
            'message' => $this->message,
            'code' => $this->code,
        ];

        if ($this->messages !== null) {
            $viewModel['messages'] = $this->messages;
        }

        if ($this->debug !== null) {
            $viewModel['debug'] = $this->debug->toArray();
        }

        return $viewModel;
    }
}