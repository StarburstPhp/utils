<?php declare(strict_types=1);

namespace Starburst\Utils\Exception;

final class MissingRequired extends \InvalidArgumentException
{
	public static function field(string $field): self
	{
		return new self(sprintf('Missing required field: "%s"', $field));
	}

	/**
	 * @param list<string> $missing
	 */
	public static function fields(array $missing): self
	{
		return new self('Missing required fields. Need ' . implode(', ', $missing));
	}
}
