<?php

namespace WordPressPluginFramework\View;

use WordPressPluginFramework\View\Model\ModelInterface;

readonly class View implements ViewInterface
{
	protected string $file;

	public function __construct(
		string $dir,
		string $file,
		protected ModelInterface $model,
	) {
		$dir = realpath($dir);
		if ($dir === false) {
			throw new ViewException('dir not found');
		}

		$file = realpath($dir . DIRECTORY_SEPARATOR . $file);
		if ($file === false) {
			throw new ViewException('file not found');
		}

		if (!is_file($file)) {
			throw new ViewException('not a file');
		}


		if (!str_starts_with($file, $dir . DIRECTORY_SEPARATOR)) {
			throw new ViewException('file outside dir');
		}

		$this->file = $file;
	}

	public function file(): string
	{
		return $this->file;
	}

	public function model(): ModelInterface
	{
		return $this->model;
	}
}
