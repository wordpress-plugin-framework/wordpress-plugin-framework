<?php

namespace WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

use WordPressPluginFramework\Http\Message\Headers\Parameters\ParametersInterface;
use Closure;
use Stringable;

interface MediaTypeInterface extends Stringable
{
	public function type(): string;
	public function withType(string $type): static;

	public function subtype(): string;
	public function withSubtype(string $subtype): static;

	public function parameters(): ParametersInterface;
	public function withParameters(ParametersInterface|Closure $parameters): static;
}
