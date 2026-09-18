<?php

namespace WordPressPluginFramework\View;

use WordPressPluginFramework\View\Model\ModelInterface;

readonly class ViewFactory implements ViewFactoryInterface
{
	public function __construct(
		protected string $dir,
	) {
	}

	public function create(string $view, ModelInterface $model): ViewInterface
	{
		$file = str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php';

		return new View($this->dir, $file, $model);
	}
}
