<?php

namespace WordPressPluginFramework\View;

use WordPressPluginFramework\View\Model\ModelInterface;

interface ViewFactoryInterface
{
    public function create(string $view, ModelInterface $model): ViewInterface;
}
