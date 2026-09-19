<?php

namespace WordPressPluginFramework\Http\Abnf;

readonly class Rfc9110
{
	public const OWS = '(?:' . Rfc5234::SP . '|' . Rfc5234::HTAB . ')*';
	public const TCHAR = '(?:!|#|\$|%|&|\'|\*|\+|-|\.|\^|_|`|\||~|' . Rfc5234::DIGIT . '|' . Rfc5234::ALPHA . ')';
	public const TOKEN = '(?:' . self::TCHAR . ')+';
	public const OBS_TEXT = '[\x80-\xFF]';
	public const QDTEXT = '(?:' . Rfc5234::HTAB . '|' . Rfc5234::SP . '|!|[\x23-\x5B]|[\x5D-\x7E]|' . self::OBS_TEXT . ')';
	public const QUOTED_PAIR = '\\\\(?:' . Rfc5234::HTAB . '|' . Rfc5234::SP . '|' . Rfc5234::VCHAR . '|' . self::OBS_TEXT . ')';
	public const QUOTED_STRING = Rfc5234::DQUOTE . '(?:' . self::QDTEXT . '|' . self::QUOTED_PAIR . ')*' . Rfc5234::DQUOTE;
	public const FIELD_NAME = '(?<field_name>' . self::TOKEN . ')';
	public const FIELD_VCHAR = '(?:' . Rfc5234::VCHAR . '|' . self::OBS_TEXT . ')';
	public const FIELD_CONTENT = self::FIELD_VCHAR . '(?:(?:' . Rfc5234::SP . '|' . Rfc5234::HTAB . '|' . self::FIELD_VCHAR . ')+' . self::FIELD_VCHAR . ')?';
	public const FIELD_VALUE = '(?:' . self::FIELD_CONTENT . ')*';
	public const QVALUE = '(?:(?:0(?:\.(?:' . Rfc5234::DIGIT . '){0,3})?)|(?:1(?:\.(?:0){0,3})?))';
	public const TYPE = '(?<type>' . self::TOKEN . ')';
	public const SUBTYPE = '(?<subtype>' . self::TOKEN . ')';
	public const MEDIA_TYPE = self::TYPE . '/' . self::SUBTYPE . self::PARAMETERS;
	public const CONTENT_TYPE = self::MEDIA_TYPE;
	public const PARAMETER_NAME = '(?<parameter_name>' . self::TOKEN . ')';
	public const PARAMETER_VALUE = '(?<parameter_value>' . self::TOKEN . '|' . self::QUOTED_STRING . ')';
	public const PARAMETER = self::PARAMETER_NAME . '=' . self::PARAMETER_VALUE;
	public const PARAMETERS = '(?<parameters>(?:' . self::OWS . ';' . self::OWS . '(?:' . self::PARAMETER . ')?)*)';
	public const WEIGHT = self::OWS . ';' . self::OWS . '(?:Q|q)=(?<q>' . self::QVALUE . ')';
	public const MEDIA_RANGE = '(?<media_range>' . self::TYPE . '/' . self::SUBTYPE . self::PARAMETERS . ')';
	public const VARY = '(?:' . self::FIELD_NAME . ')?(?:' . self::OWS . ',' . self::OWS . '(?:' . self::FIELD_NAME . ')?)*';
	public const ACCEPT = '(?:(?:' . self::MEDIA_RANGE . '(?:' . self::WEIGHT . ')?))?(?:' . self::OWS . ',' . self::OWS . '(?:(?:' . self::MEDIA_RANGE . '(?:' . self::WEIGHT . ')?))?)*';
}
