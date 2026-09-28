<?php

namespace WordPressPluginFramework\Hooks;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\PipelineFactoryInterface,
	Renderer\RendererInterface,
};

readonly class HooksBuilder implements HooksBuilderInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected RendererInterface $renderer,
		protected PipelineFactoryInterface $pipelineFactory,
		protected array $hooks = [],
	) {
	}

	public function withHook(HookInterface $hook): static
	{
		$hooks = $this->hooks;
		$hooks[] = $hook;

		return new static($this->request, $this->renderer, $this->pipelineFactory, $hooks);
	}

	public function action(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static
	{
		$hook = new Action\Hook($this->request, $this->renderer, $this->pipelineFactory, $name, $closure, $priority, $middlewaresBuilderClosure);
		return $this->withHook($hook);
	}

	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static
	{
		$hook = new Filter\Hook($this->request, $this->renderer, $this->pipelineFactory, $name, $closure, $priority, $middlewaresBuilderClosure);
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

	public function build(): HooksInterface
	{
		return new Hooks($this->hooks);
	}
}
