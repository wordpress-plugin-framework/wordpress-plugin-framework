<?php

namespace WordPressPluginFramework\Http\Message\Headers\Accept;

interface AcceptFactoryInterface
{
	public function create(string $accept): AcceptInterface;
}
