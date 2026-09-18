<?php

namespace WordPressPluginFramework\Http\Abnf;

readonly class Rfc6838
{
	public const TYPE_NAME = self::RESTRICTED_NAME;
	public const SUBTYPE_NAME = self::RESTRICTED_NAME;
	public const RESTRICTED_NAME = self::RESTRICTED_NAME_FIRST . '(?:' . self::RESTRICTED_NAME_CHARS . '){0,126}';
	public const RESTRICTED_NAME_FIRST = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . ')';
	public const RESTRICTED_NAME_CHARS = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|!|#|\$|&|-|\^|_|\.|\+)';
	public const PARAMETER_NAME = self::RESTRICTED_NAME;
}
