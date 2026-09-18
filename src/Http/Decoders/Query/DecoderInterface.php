<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders\Query;

interface DecoderInterface
{
	public function decode(string $query): mixed;
}
