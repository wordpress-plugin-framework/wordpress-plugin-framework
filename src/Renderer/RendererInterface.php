<?php

namespace WordPressPluginFramework\Renderer;

use WordPressPluginFramework\View\ViewInterface;

interface RendererInterface
{
	public function render(ViewInterface $view): string;
}
