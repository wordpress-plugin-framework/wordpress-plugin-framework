<?php

declare(strict_types=1);

namespace WordPressPluginFramework\View;

use WordPressPluginFramework\View\Model\ModelInterface;

interface ViewInterface
{
	public function file(): string;
	public function model(): ModelInterface;
}
