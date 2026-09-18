<?php

namespace Hoo\WordPressPluginFramework\Http\Abnf;

readonly class Rfc2046
{
	public const BCHARSNOSPACE = '(?:' . Rfc5234::DIGIT . '|' . Rfc5234::ALPHA . '|\'|\(|\)|\+|_|,|-|\.|/|:|=|\?)';
	public const BCHARS = '(?:' . self::BCHARSNOSPACE . '|' . Rfc5234::SP . ')';
	public const BOUNDARY = '(?:' . self::BCHARS . '){0,69}' . self::BCHARSNOSPACE;
}
