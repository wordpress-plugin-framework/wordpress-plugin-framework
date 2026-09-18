<?php

namespace WordPressPluginFramework\Exceptions\Handler\ViewModels\Debug;

use WordPressPluginFramework\View\Model\ModelInterface as ViewModelInterface;
use Throwable;

readonly class ViewModel implements ViewModelInterface
{
    protected function __construct(
        public string $class,
        public string $file,
        public int $line,
        public array $trace,
    ) {
    }

    public static function createFromThrowable(Throwable $throwable): static
    {
        return new static(
                $throwable::class,
            $throwable->getFile(),
            $throwable->getLine(),
            $throwable->getTrace(),
        );
    }

    public function toArray(): array
    {
        $viewModel = [
            'class' => $this->class,
            'file' => $this->file,
            'line' => $this->line,
            'trace' => $this->trace,
        ];

        return $viewModel;
    }
}