<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Query\Decoders;

interface DecoderInterface
{
	public function squareBrackets(): bool;

	public function decode(string $query): mixed;
}
