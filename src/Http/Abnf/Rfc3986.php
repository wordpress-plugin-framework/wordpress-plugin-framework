<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Abnf;

readonly class Rfc3986
{
	public const AUTHORITY = '(?:(?:' . self::USERINFO . '\@)?' . self::HOST . '(?::' . self::PORT . ')?)';
	public const USERINFO = '(?<userinfo>(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . '|:)*)';
	public const HOST = '(?<host>' . self::IP_LITERAL . '|' . self::IPV4ADDRESS . '|' . self::REG_NAME . ')';
	public const PORT = '(?<port>(?:' . Rfc5234::DIGIT . ')*)';
	public const IP_LITERAL = '(?<ip_literal>\[(?:' . self::IPV6ADDRESS . '|' . self::IPVFUTURE . ')\])';
	public const IPVFUTURE = '(?:(?:v|V)(?:' . Rfc5234::HEXDIG . ')+\.(?:' . self::UNRESERVED . '|' . self::SUB_DELIMS . '|:)+)';
	public const IPV6ADDRESS = '(?:'
		. '(?:' . self::H16 . ':){6}' . self::LS32
		. '|::(?:' . self::H16 . ':){5}' . self::LS32
		. '|(?:' . self::H16 . ')?::(?:' . self::H16 . ':){4}' . self::LS32
		. '|(?:(?:' . self::H16 . ':){0,1}' . self::H16 . ')?::(?:' . self::H16 . ':){3}' . self::LS32
		. '|(?:(?:' . self::H16 . ':){0,2}' . self::H16 . ')?::(?:' . self::H16 . ':){2}' . self::LS32
		. '|(?:(?:' . self::H16 . ':){0,3}' . self::H16 . ')?::' . self::H16 . ':' . self::LS32
		. '|(?:(?:' . self::H16 . ':){0,4}' . self::H16 . ')?::' . self::LS32
		. '|(?:(?:' . self::H16 . ':){0,5}' . self::H16 . ')?::' . self::H16
		. '|(?:(?:' . self::H16 . ':){0,6}' . self::H16 . ')?::'
		. ')';
	public const H16 = '(?:' . Rfc5234::HEXDIG . '){1,4}';
	public const LS32 = '(?:(?:' . self::H16 . ':' . self::H16 . ')|' . self::IPV4ADDRESS . ')';
	public const IPV4ADDRESS = '(?:' . self::DEC_OCTET . '\.' . self::DEC_OCTET . '\.' . self::DEC_OCTET . '\.' . self::DEC_OCTET . ')';
	public const DEC_OCTET = '(?:' . Rfc5234::DIGIT . '|[\x31-\x39]' . Rfc5234::DIGIT . '|1(?:' . Rfc5234::DIGIT . '){2}|2[\x30-\x34]' . Rfc5234::DIGIT . '|25[\x30-\x35])';
	public const REG_NAME = '(?<reg_name>(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . ')*)';
	public const PATH_ABEMPTY = '(?<path_abempty>(?:/' . self::SEGMENT . ')*)';
	public const SEGMENT = '(?<segment>(?:' . self::PCHAR . ')*)';
	public const PCHAR = '(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . '|:|\@)';
	public const QUERY = '(?<query>(?:' . self::PCHAR . '|/|\?)*)';
	public const PCT_ENCODED = '(?:%' . Rfc5234::HEXDIG . Rfc5234::HEXDIG . ')';
	public const UNRESERVED = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|-|\.|_|~)';
	public const SUB_DELIMS = '(?:!|\$|&|\'|\(|\)|\*|\+|,|;|=)';
	public const PCT_ENCODED_UNRESERVED = '%(?:4[1-9A-F]|5[0-9A]|6[1-9A-F]|7[0-9A]|3[0-9]|2D|2E|5F|7E)';
	public const APPENDIX_B = '^((?<scheme>[^:/?#]+):)?(//(?<authority>[^/?#]*))?(?<path>[^?#]*)(\?(?<query>[^#]*))?(#(?<fragment>.*))?';
}
