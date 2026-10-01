<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Hooks;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\PipelineBuilderInterface,
	Renderer\RendererInterface,
};

readonly class HooksBuilder implements HooksBuilderInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected RendererInterface $renderer,
		protected PipelineBuilderInterface $pipelineBuilder,
		protected array $hooks = [],
	) {
	}

	public function withHook(HookInterface $hook): static
	{
		$hooks = $this->hooks;
		$hooks[] = $hook;

		return new static($this->request, $this->renderer, $this->pipelineBuilder, $hooks);
	}

	public function group(Closure $closure, ?Closure $pipelineBuilderClosure = null): static
	{
		$pipelineBuilder = $pipelineBuilderClosure === null ? $this->pipelineBuilder : $pipelineBuilderClosure($this->pipelineBuilder);
		if (!$pipelineBuilder instanceof PipelineBuilderInterface) {
			throw new HooksBuilderException('closure must return pipeline builder instance');
		}

		$hooksBuilder = $closure(new static($this->request, $this->renderer, $pipelineBuilder));
		if (!$hooksBuilder instanceof HooksBuilderInterface) {
			throw new HooksBuilderException('closure must return hooks builder instance');
		}

		return $this->withHook($hooksBuilder->build());
	}

	public function action(string $name, Closure $closure, int $priority = 10, ?Closure $pipelineBuilderClosure = null): static
	{
		$hook = new Action\Hook($this->request, $this->renderer, $this->pipelineBuilder, $name, $closure, $priority, $pipelineBuilderClosure);
		return $this->withHook($hook);
	}

	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $pipelineBuilderClosure = null): static
	{
		$hook = new Filter\Hook($this->request, $this->renderer, $this->pipelineBuilder, $name, $closure, $priority, $pipelineBuilderClosure);
		return $this->withHook($hook);
	}

	public function activation(string $file, Closure $closure): static
	{
		$hook = new Activation\Hook($file, $closure);
		return $this->withHook($hook);
	}

	public function deactivation(string $file, Closure $closure): static
	{
		$hook = new Deactivation\Hook($file, $closure);
		return $this->withHook($hook);
	}

	public function build(): HookInterface
	{
		return new Hooks($this->hooks);
	}
}
