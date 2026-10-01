<?php

declare(strict_types=1);

namespace WordPressPluginFramework\View;

use WordPressPluginFramework\View\Model\ModelInterface;

interface ViewFactoryInterface
{
    public function create(string $view, ModelInterface $model): ViewInterface;
}
