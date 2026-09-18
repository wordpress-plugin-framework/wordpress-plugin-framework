<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\Query;

interface EncoderInterface
{
	public function encode(mixed $query): string;
}
