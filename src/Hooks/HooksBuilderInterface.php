<?php

namespace WordPressPluginFramework\Hooks;

use Closure;

interface HooksBuilderInterface
{

	public function hooks(): array;
	public function withHooks(HookInterface ...$hooks): static;
	public function withoutHooks(): static;

	public function withHook(HookInterface $hook): static;

	public function action(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static;
	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static;

	public function activation(string $file, Closure $closure): static;
	public function deactivation(string $file, Closure $closure): static;

	public function build(): array;
}
