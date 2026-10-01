<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Query;

use WordPressPluginFramework\{
	Http\Abnf\Rfc9110,
	Http\Accessor\AccessorInterface,
	Http\Normalizers\NormalizersInterface,
	Http\Url\Query\Decoders\DecodersInterface,
	Http\Url\Query\Encoders\EncodersInterface,
	Preg\PregInterface,
};

readonly class QueryFactory implements QueryFactoryInterface
{
	public function __construct(
		protected PregInterface $preg,
		protected AccessorInterface $accessor,
		protected DecodersInterface $decoders,
		protected EncodersInterface $encoders,
		protected NormalizersInterface $normalizers,
	) {
	}

	public function create(mixed $query, bool $squareBrackets = true): QueryInterface
	{
		if ($query instanceof QueryInterface) {
			$query = $query();
		}

		$normalizedQuery = $this->normalizers->normalize($query);

		$encodersBySquareBrackets = $this->encoders->filter(fn($encoder) => $encoder->squareBrackets() === $squareBrackets);
		if ($encodersBySquareBrackets->isEmpty()) {
			throw new QueryFactoryException('no encoder for these square brackets');
		}

		$encodersByQuery = $encodersBySquareBrackets->filter(fn($encoder) => $encoder->encodesQuery($normalizedQuery));
		if ($encodersByQuery->isEmpty()) {
			throw new QueryFactoryException('no encoder for this query');
		}

		$encoder = $encodersByQuery->first();

		return new Query($this->accessor, $encoder, $normalizedQuery);
	}

	public function createFromEncoded(string $query, bool $squareBrackets = true): QueryInterface
	{
		$match = $this->preg->match('@\A' . Rfc9110::QUERY . '\z@', $query);
		if ($match === null) {
			throw new QueryFactoryException('invalid query');
		}

		$decoder = $this->decoders
			->filter(fn($decoder) => $decoder->squareBrackets() === $squareBrackets)
			->first();

		$decodedQuery = $decoder->decode($match['query']);

		return $this->create($decodedQuery, $squareBrackets);
	}
}
