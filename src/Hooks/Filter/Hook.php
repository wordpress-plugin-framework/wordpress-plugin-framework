<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Hooks\Filter;

use Closure;
use WordPressPluginFramework\{
	Hooks\HookInterface,
	Http\Request\RequestInterface,
	Pipeline\PipelineInterface,
	Pipeline\PipelineBuilderInterface,
	Renderer\RendererInterface,
	View\ViewInterface,
};

readonly class Hook implements HookInterface
{
	protected PipelineInterface $pipeline;

	public function __construct(
		protected RequestInterface $request,
		protected RendererInterface $renderer,
		protected PipelineBuilderInterface $pipelineBuilder,
		protected string $name,
		protected Closure $closure,
		protected int $priority = 10,
		protected ?Closure $pipelineBuilderClosure = null,
	) {
	}

	public function __invoke(): void
	{
		add_filter(
			$this->name,
			$this->callback(...),
			$this->priority,
			PHP_INT_MAX,
		);
	}

	protected function callback(...$args): mixed
	{
		$pipeline = $this->pipeline ??= $this->pipelineBuilder()->build();

		$view = $pipeline(fn($request) => ($this->closure)($request, ...$args));
		if ($view instanceof ViewInterface) {
			return $this->renderer->render($view);
		}

		return $view;
	}

	protected function pipelineBuilder(): PipelineBuilderInterface
	{
		return $this->pipelineBuilderClosure === null ? $this->pipelineBuilder : ($this->pipelineBuilderClosure)($this->pipelineBuilder);
	}
}
