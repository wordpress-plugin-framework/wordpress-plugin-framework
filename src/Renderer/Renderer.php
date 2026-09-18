<?php

namespace WordPressPluginFramework\Renderer;

use WordPressPluginFramework\{
	View\Model\ModelInterface,
	Renderer\Escaper\EscaperInterface,
	View\ViewInterface,
};
use Throwable;

readonly class Renderer implements RendererInterface
{
	public function __construct(
		protected EscaperInterface $escaper,
	) {
	}

	public function render(ViewInterface $view): string
	{
		$file  = $view->file();
		$model = $view->model();

		ob_start();

		try {
			self::require($this->escaper, $file, $model);
		} catch (Throwable $throwable) {
			ob_end_clean();

			throw $throwable;
		}

		$ob = ob_get_clean();
		if ($ob === false) {
			throw new RendererException('Failed to capture view output');
		}

		return $ob;
	}

	protected static function require(EscaperInterface $escaper, string $file, ModelInterface $model): void
	{
		require($file);
	}
}
