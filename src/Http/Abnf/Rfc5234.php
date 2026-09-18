<?php

namespace WordPressPluginFramework\Http\Abnf;

readonly class Rfc5234
{
	public const ALPHA = '(?:[\x41-\x5A]|[\x61-\x7A])';
	public const BIT = '(?:0|1)';
	public const CHAR = '[\x01-\x7F]';
	public const CR = '\x0D';
	public const CRLF = self::CR . self::LF;
	public const CTL = '(?:[\x00-\x1F]|\x7F)';
	public const DIGIT = '[\x30-\x39]';
	public const DQUOTE = '\x22';
	public const HEXDIG = '(?:' . self::DIGIT . '|(?:A|a)|(?:B|b)|(?:C|c)|(?:D|d)|(?:E|e)|(?:F|f))';
	public const HTAB = '\x09';
	public const LF = '\x0A';
	public const LWSP = '(?:' . self::WSP . '|' . self::CRLF . self::WSP . ')*';
	public const OCTET = '[\x00-\xFF]';
	public const SP = '\x20';
	public const VCHAR = '[\x21-\x7E]';
	public const WSP = '(?:' . self::SP . '|' . self::HTAB . ')';
}
