<?php

namespace WordPressPluginFramework\Http\Message\Headers\Parameters;

interface ParametersFactoryInterface
{
	public function create(string $parameters): ParametersInterface;
}
