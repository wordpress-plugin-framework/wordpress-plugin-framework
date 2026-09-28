<?php

namespace WordPressPluginFramework\Http\Abnf;

readonly class Rfc3629
{
	public const UTF8_2 = '(?:[\xC2-\xDF]' . self::UTF8_TAIL . ')';
	public const UTF8_3 = '(?:\xE0[\xA0-\xBF]' . self::UTF8_TAIL . '|[\xE1-\xEC]' . self::UTF8_TAIL . '{2}|\xED[\x80-\x9F]' . self::UTF8_TAIL . '|[\xEE-\xEF]' . self::UTF8_TAIL . '{2})';
	public const UTF8_4 = '(?:\xF0[\x90-\xBF]' . self::UTF8_TAIL . '{2}|[\xF1-\xF3]' . self::UTF8_TAIL . '{3}|\xF4[\x80-\x8F]' . self::UTF8_TAIL . '{2})';
	public const UTF8_TAIL = '[\x80-\xBF]';
}
