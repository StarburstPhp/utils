<?php declare(strict_types=1);

namespace Starburst\Utils;

final class Json
{
	public static function encode(mixed $value): string
	{
		return json_encode($value, JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR|JSON_INVALID_UTF8_SUBSTITUTE);
	}

	public static function decode(string $value): mixed
	{
		return json_decode(
			$value,
			associative: true,
			flags: JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR|JSON_INVALID_UTF8_SUBSTITUTE,
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function decodeArray(string $value): array
	{
		$output = self::decode($value);
		if (!is_array($output)) {
			throw new \JsonException('Output is not an array');
		}
		return $output;
	}

	/**
	 * @return list<mixed>
	 */
	public static function decodeList(string $value): array
	{
		$output = self::decode($value);
		if (!is_array($output) || (!function_exists('array_is_list') || !array_is_list($output))) {
			throw new \JsonException('Output is not an list');
		}
		return $output;
	}
}
