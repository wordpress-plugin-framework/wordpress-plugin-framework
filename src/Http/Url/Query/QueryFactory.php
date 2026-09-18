<?php

namespace WordPressPluginFramework\Http\Url\Query;

use WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Decoders\Query\DecoderInterface,
	Http\Encoders\Query\EncoderInterface,
	Http\Normalizers\NormalizersInterface,
};
use stdClass;

readonly class QueryFactory implements QueryFactoryInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected DecoderInterface $decoder,
		protected EncoderInterface $encoder,
		protected NormalizersInterface $normalizers,
	) {
	}

	public function create(mixed $query): QueryInterface
	{
		if ($query instanceof QueryInterface) {
			$query = $query();
		}

		$normalizedQuery = $this->normalizers->normalize($query);

		return $this->query($normalizedQuery);
	}

	public function createFromEncoded(string $query): QueryInterface
	{
		$decodedQuery = $this->decoder->decode($query);

		return $this->create($decodedQuery);
	}

	protected function query(mixed $query): QueryInterface
	{
		if (
			!is_array($query) &&
			!$query instanceof stdClass
		) {
			throw new QueryFactoryException('no encoder for this query');
		}

		return new Query($this->accessor, $this->encoder, $query);
	}
}
